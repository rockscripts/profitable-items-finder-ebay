<?php
include "config.php";
$country_code = trim($_REQUEST['country_code']);
$toNumber = "+" . $country_code . trim($_REQUEST["number"]);
$varification_code = trim($_REQUEST['verification_code']);
$device_token = trim($_REQUEST['device_token']);
$phone = $country_code . trim($_REQUEST["number"]);


$counter = count($sd);
$sql = "select id from verification where phone_no ='$toNumber' and verification_code='$varification_code'";
$res = mysql_query($sql) or die(mysql_error());
$count = mysql_num_rows($res);
//$row = mysql_fetch_assoc($res);
$result = array();
if ($count == 1) {
    $result['success'] = 'true';
    $result['message'] = 'You are successfully registered';
//		$result['user_id'] = $row['id'];
//		$result['count_sit'] = $counter;
    mysql_query("update verification set status = 1, device_token='$device_token' where phone_no ='$toNumber'");
} else {
    $result['success'] = 'false';
    $result['message'] = 'Please registered your number';
}
echo json_encode($result);
?>