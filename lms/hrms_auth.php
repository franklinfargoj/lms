<?php
echo $_GET['username'];die;
$url = 'https://103.224.111.35/lms/hrms_auth.php?username='.$_GET['username'].'&password='.$_GET['password'];
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_exec($ch);
$result = curl_exec($ch);
echo $result;
?>