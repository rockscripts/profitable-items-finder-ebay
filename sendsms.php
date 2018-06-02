<?php
require "Services/Twilio.php";
include "config.php";
	
    $file = fopen("sms.txt", "w");
	$code = rand(100000, 999999);
	$toNumber = "+573177246661"/*.$_REQUEST["phone_number"]*/;
        $toNumber = str_replace("(","",$toNumber);
        $toNumber = str_replace(")","",$toNumber);
//echo $toNumber;exit;
        fwrite($file, "StoryTeller Verify $code $toNumber" );

	$sql 	= mysql_query("select phone_no from verification where phone_no = '$toNumber'") or die(mysql_error());
	$count 	= mysql_num_rows($sql);
	$json = array();
	if($count == 1){
		
		$AccountSid = "ACd36a777f72b52f005f6585cbafb0aa47";
		$AuthToken  = "8073c8139a7779d747c93fde42db592f";	
		$fromNumber = "+34675303127";
		$client = new Services_Twilio($AccountSid, $AuthToken);	
		
		try{
   		 	$sms = $client->account->messages->sendMessage($fromNumber, $toNumber, $code);
			if($sms){
				$sql = "update verification set verification_code = '$code', status = 0, device_token = 0 where phone_no='$toNumber'";
				mysql_query($sql) or die(mysql_error());
				$json["success"] = 'true';
				$json["verification_code"] = $code;
				$json["message"] = "verification code send";
			}else{
				$json["success"] = 'false';	
				$json["message"] = 'Undelivered';
			}
		} catch (Exception $e) {
			$json["success"] = 'false';	
			$json["message"] = 'Undelivered';
		}
}
	else
	{
		$AccountSid = "AC3d1f3a68847f5e319bf64eaa8e716e17";
		$AuthToken  = "2d9daf05e425278c5d0e1e6be29ddedf";	
		$fromNumber = "+16055938495";
		$client = new Services_Twilio($AccountSid, $AuthToken);
		try 
			{
   				 $sms = $client->account->messages->sendMessage($fromNumber, $toNumber, $code);
		if($sms)
			{
                    echo "in";
				$sql = "insert into verification(phone_no, verification_code, status, device_token, paypal_id)VALUES('$toNumber', $code, 0, 0, '')";
				mysql_query($sql);
				$json["success"] = 'true';
				$json["verification_code"] = $code;
				$json["message"] = "verification code send";
			}
			else
			{
				$json["success"] = 'false';	
				$json["message"] = 'invalid mobile number';
			}
		} catch (Exception $e) {
			$json["success"] = 'false';	
			$json["message"] = 'invalid mobile number';
	}
}

		header('Content-type: application/json');
		echo(json_encode($json));
?>
