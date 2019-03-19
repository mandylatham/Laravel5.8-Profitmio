<?php

return [

    /**
     * The phone number used to send out SMS notifications.
     */
    'from_number' => env('PHONE_VERIFICATION_NUMBER'),

    /**
     * Confirmation message to send
     */
    'confirmation_message' => 'Thank you for verifying your number. Profitminer will send you SMS notifications. To opt out at any time, reply to this number with STOP',

    /**
     * The action to perform when the attempts threshold has been reached. The options are:
     * 
     * "block" which permenently blocks the number from being validated
     * "delay" which prevents the number from being validated for a period of time
     * "none" to disable throttling
     */
    'throttle_action' => 'delay',

    /**
     * This is the number of failed attempts to allow before taking the throttle action
     */
    'throttle_attempts' => 3,

    /**
     * The number of seconds to delay the next validation attempt. Default is 86400 (1 day).
     */
    'throttle_delay' => 30,
];