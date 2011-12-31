<?php
// http://davidwalsh.name/execute-http-post-php-curl
$url = 'http://www.gogomenu.com/login/main&goto=home/main';

$fields = array
(
    'lu' => 'admin',
    'lp' => 'henry',
);

$fields['lu'] .= 'fda';

//url-ify the data for the POST
$fields_string = '';
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string,'&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_HTTPHEADER,array('splatnom: unittest'));
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_POST,count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

//execute post
$result = curl_exec($ch);

$info = curl_getinfo($ch);

error_log(var_export($info, true));

//close connection
curl_close($ch);
