<?php
$url = 'http://172.25.2.35:8001/PSIGW/RESTListeningConnector/DBK_BRANCH_DETAILS.v1/?call=0';
//$url = 'http://172.25.2.35:8001/PSIGW/RESTListeningConnector/dbk_lms_emp_all.v1/?hrms_id=0000000';
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_exec($ch);
if(curl_error($ch)){
echo "error : ".curl_error($ch);die;
}else{
$result = curl_exec($ch);
echo $result;
die;
//$details = json_decode($result);
//echo $details;
//die;
//print_r($auth);

switch(json_last_error()){
case JSON_ERROR_NONE:
echo 'NONE';
break;
case JSON_ERROR_DEPTH:
echo '-max';
break;
case JSON_ERROR_STATE_MISMATCH:
echo 'JSON_ERROR_STATE_MISMATCH';
break;
case JSON_ERROR_CTRL_CHAR:
echo 'JSON_ERROR_CTRL_CHAR';
break;
case JSON_ERROR_CTRL_CHAR:
echo 'JSON_ERROR_CTRL_CHAR';
break;
case JSON_ERROR_SYNTAX:
echo 'Syntax error,malformed JSON';
break;
case JSON_ERROR_UTF8:
echo '- Malformed UTF-8 Character, possibly incorrectly encoded';
break;
default:
echo 'def';
break;
}
}
?>
