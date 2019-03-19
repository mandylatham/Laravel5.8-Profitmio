<?php
namespace App\Services;

use Exception;
use Illuminate\Log\Logger;
use Illuminate\Support\Str;
use App\Models\PhoneVerification;
use Twilio\Exceptions\HttpException;
use Twilio\Exceptions\TwilioException;
use App\Exceptions\PhoneVerification\SmsException;
use App\Exceptions\PhoneVerification\BlockedException;
use App\Exceptions\PhoneVerification\DelayedException;
use App\Exceptions\PhoneVerification\AlreadyStartedException;
use App\Exceptions\PhoneVerification\AlreadyVerifiedException;
use App\Exceptions\PhoneVerification\InvalidConfigurationException;

class PhoneVerificationService
{
    /**
     * The number to send the verification from
     */
    private $config;

    /**
     * The Logger
     */
    private $log;

    /**
     * The phone verification
     */
    private $phone;

    /**
     * The Service to send SMS messages
     */
    private $smsService;

    /**
     * The constructor.
     * 
     * @param TwilioClient $smsService
     */
    public function __construct(TwilioClient $smsService, Logger $log)
    {
        $this->config = (object)config('phone-verification');
        $this->log = $log;
        $this->smsService = $smsService;
        $this->verifyConfig();
    }

    /**
     * Start the Verification Process
     * 
     * @param string $phoneNumber The Phone Number to verify
     * 
     * @return string
     */
    public function start($phoneNumber): string
    {
        $this->phone = $this->getPhoneVerification($phoneNumber);

        $this->log->debug(json_encode($this->phone));
        if (! $this->phone->started) {
            $this->phone->start();
            try {
                $this->send();
            } catch (TwilioException $e) {
                $message = $e instanceof HttpException 
                    ? 'PhoneVerificationService: [Twilio]: ' . $e->getMessage()
                    : 'PhoneVerificationService: [Twilio HTTP]: ' . $e->getMessage();
                $this->fail($message);
                throw new SmsException;
            } catch (Exception $e) {
                $message = 'PhoneVerificationService reported an error sending verification: ' . $e->getMessage();
                $this->fail($message);
                throw new Exception($message);
            }

            return PhoneVerification::STARTED;
        }

        switch ($this->phone->status) {
            case PhoneVerification::VERIFIED:
                throw new AlreadyVerifiedException;
            case PhoneVerification::FAILED:
                if ($this->config->throttle_action == 'block') {
                    throw new BlockedException;
                } elseif ($this->config->throttle_action === 'delay') {
                    throw new AlreadyStartedException;
                }
                break;
            case PhoneVerification::WAITING:
            case PhoneVerification::STARTED:
                throw new AlreadyStartedException;
        }
    }

    /**
     * Is the verification code valid?
     * 
     * @param string $phoneNumber      The Phone Number to verify
     * @param string $verificationCode The verificaiton code to evaluate
     * 
     * @return bool
     */
    public function verify($phoneNumber, $verificationCode): bool
    {
        $this->phone = $this->getPhoneVerification($phoneNumber);

        if ($this->phone->verified) {
            throw new AlreadyVerifiedException;
        }

        $this->checkThrottling();

        if ($this->phone->code === $verificationCode) {
            $this->phone->verify();
            $this->confirm();
            return true;
        }

        $this->phone->attempt();
        if ($this->tooManyAttempts()) {
            $this->phone->fail(Str::upper($this->config->throttle_action) . ": Too many invalid codes entered");
        }

        return false;
    }

    private function fail($message)
    {
        $this->log->error($message);
        $this->phone->fail($message);
    }

    /**
     * Send the new phone number the confirmation message with opt-out text
     * 
     * @return void
     */
    private function confirm(): void
    {
        $this->smsService->sendSms(
            $this->config->from_number,
            $this->phone->phone,
            $this->config->confirmation_message);
    }

    /**
     * Send the code
     * 
     * @return void
     */
    private function send(): void
    {
        $this->smsService->sendSms(
            $this->config->from_number,
            $this->phone->phone,
            $this->phone->code);
    }

    /**
     * Obtain a PhoneVerification from a phone number
     * 
     * @param string $phoneNumber The Phone Number to search or create
     * 
     * @return PhoneVerification
     */
    private function getPhoneVerification($phoneNumber): PhoneVerification
    {
        $phone = $this->uniformPhone($phoneNumber);
        return PhoneVerification::firstOrCreate(['phone' => $phone], ['code' => rand(100000, 999999)]);
    }

    /**
     * Check if the verification can be sent
     */
    private function checkThrottling(): void
    {
        switch ($this->config->throttle_action) {
            case 'block':
                if ($this->tooManyAttempts()) {
                    $this->log->info("PhoneVerificationService: verificatons blocked for phone number ({$this->phone->phone})");
                    throw new BlockedException;
                }
                break;
            case 'delay':
                if ($this->phone->failed) {
                    if ($this->delayPeriodExceeded()) {
                        $this->log->info("PhoneVerificationService: verificaton delay reset for phone number ({$this->phone->phone})");
                        $this->phone->reset();
                    } else {
                        $remaining = $this->config->throttle_delay - $this->delayPeriodElapsed();
                        $message = "PhoneVerificationService: verificatons delayed for {$remaining} seconds on phone number ({$this->phone->phone})";
                        $this->log->info($message);
                        throw new DelayedException("Verifications for this number locked for {$remaining} seconds");
                    }
                }
                break;
            case 'none':
                $this->log->debug("PhoneVerificationService: no throttling enabled");
        }
    }

    /**
     * Should this verification be throttled?
     * 
     * @return bool
     */
    private function tooManyAttempts(): bool
    {
        $this->log->debug("attempts remaining: ". $this->attemptsRemaining());
        return ! $this->attemptsRemaining();
    }

    /**
     * The number of remaining attempts
     * 
     * @return int
     */
    private function attemptsRemaining(): int
    {
        return (int)max(($this->config->throttle_attempts ?: 0) - ($this->phone->attempts ?: 0), 0);
    }

    /**
     * Is the delay threshold exceeded?
     * 
     * @return bool
     */
    private function delayPeriodExceeded(): bool
    {
        return $this->delayPeriodElapsed() >= $this->config->throttle_delay;
    }

    /**
     * Get the delay period in seconds
     */
    private function delayPeriodElapsed(): int
    {
        return $this->phone->failed ? $this->phone->failed_at->diffInSeconds(now('UTC')) : 0;
    }

    /**
     * Return a uniform phone number
     * 
     * TODO: Should probably be handled by a seperate service
     * 
     * @param string $phone A phone number
     * 
     * @return string
     */
    private function uniformPhone($phone): string
    {
        $phone = str_replace('+1', '', $phone);
        $phone = preg_replace('/[^0-9]/', '', $phone);

        return $phone;
    }

    private function verifyConfig()
    {
        try {
            if (! in_array($this->config->throttle_action, ['block', 'delay', 'none']) ||
                ! is_integer($this->config->throttle_attempts) ||
                ! is_integer($this->config->throttle_delay)) {
                    throw new Exception;
                }
        } catch (Exception $e) {
            $this->log->critical("PhoneVerificationService reporting invalid config from 'config/phone-verification'");
            throw new InvalidConfigurationException;
        }
    }
}