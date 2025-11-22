<?php

include "Include/textLocal/textlocal.class.php";
include "Include/twilioLib/vendor/autoload.php";

function SendOtp($mobile, $otp){

    $textLocal = new Textlocal(false, false, 'MDY0NmMyYzAzM2JkNmMwOTRjOTVlZDBmNGIwY2IyZGU=');
 
    $mobile = substr($mobile, 1);

	$numbers = array($mobile);
	$sender = 'CARDsK';
	$message = 'Welcome to CARDsKING. Your OTP for mobile verification is '.$otp.'. Thank You, CARDsKING.';
 
	$response = $textLocal->sendSms($numbers, $message, $sender);
}

function SendOtpViaTwilio($mobile, $otp){
    // Client
    $twilioAccountSid = '';
    $twilioAccountToken = '';
    $twilioMessagingServiceSid = '';

    $toNumber = $mobile;
    $message = 'Welcome to CARDsKING. Your OTP for mobile verification is '.$otp.'. Thank You, CARDsKING.';

    $client = new Twilio\Rest\Client($twilioAccountSid, $twilioAccountToken);
    $client->messages->create($toNumber, array(
            'messagingServiceSid' => $twilioMessagingServiceSid,
            'body' => $message
        )
    );
}

?>