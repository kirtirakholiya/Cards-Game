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
    $twilioAccountSid = 'AC5e7a42356c0e36b3563f5f48a9d48d61';
    $twilioAccountToken = '38a216bf8572ae4b5657a1687e9fa77f';
    $twilioMessagingServiceSid = 'MG477c2448b29579d05ddad9d1eff6ed7b';

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