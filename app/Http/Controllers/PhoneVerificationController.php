<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhoneVerification;
use App\Services\PhoneVerificationService;
use App\Exceptions\PhoneVerification\DelayedExcetion;
use App\Exceptions\PhoneVerification\BlockedException;
use App\Exceptions\PhoneVerification\DelayedException;
use App\Exceptions\PhoneVerification\AlreadyStartedException;
use App\Exceptions\PhoneVerification\AlreadyVerifiedException;
use App\Exceptions\PhoneVerification\InvalidConfigurationException;

class PhoneVerificationController extends Controller
{
    private $service;

    /**
     * The contructor
     */
    public function __construct(PhoneVerificationService $service)
    {
        $this->service = $service;
    }

    public function sendVerificationCode(Request $request)
    {
        if (! $request->has('phone')) {
            abort(422, "Phone number not provided");
        }

        try {
            $phone = $request->input('phone');
            return response()->json([
                'status' => $this->service->start($phone),
                'message' => 'The phone number has been sent a verification code']);
        } catch (AlreadyStartedException $e) {
            return response()->json([
                'status' => 'already-started',
                'message' => 'The phone number has already been sent a verification code']);
        } catch (AlreadyVerifiedException $e) {
            return response()->json([
                'status' => 'already-verified',
                'message' => 'The phone number is already verified in our system']);
        } catch (InvalidConfigurationException $e) {
            // Send a message to Slack Channel
            abort(500, "Unable to verify number at this time");
        }
    }

    public function verify(Request $request)
    {
        if (! $request->has('phone') || ! $request->has('code')) {
            abort(422, "Missing parameters");
        }

        try {
            return response()->json([
                'status' => $this->service->verify($request->input('phone'), $request->input('code')) ? 'verified' : 'failed']);
        } catch (AlreadyVerifiedException $e) {
            return response()->json([
                'status' => 'already-verified',
                'message' => 'The phone number was already verified in our system']);
        } catch (BlockedException $e) {
            return response()->json([
                'status' => 'blocked',
                'message' => $e->getMessage()]);
        } catch (DelayedException $e) {
            return response()->json([
                'status' => 'delayed',
                'message' => $e->getMessage()]);
        } catch (InvalidConfigurationException $e) {
            // Send a message to Slack Channel
            abort(500, "Unable to verify number at this time");
        }
    }
}
