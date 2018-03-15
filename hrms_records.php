<?php
$url1 = 'https://103.224.111.35/lms/hrms_records.php?hrms_id='.$_GET['hrms_id'];
echo $url1;
$ch1 = curl_init();
curl_setopt($ch1,CURLOPT_URL,$url1);
curl_setopt($ch1,CURLOPT_POST,0);
curl_setopt($ch1,CURLOPT_RETURNTRANSFER,true);
curl_exec($ch1);
$result1 = curl_exec($ch1);
echo $result1;
?>