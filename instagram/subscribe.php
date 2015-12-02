<?php


//ALL YOUR IMPORTANT API INFO
$client_id = 'e9e853cde7bf4e7eb4fb036b2a495e97';
$client_secret = 'c624d87e4032487b8f370b5ebe23d8ab';
$object = 'tag';
$object_id = 'manicmachine';
$aspect = 'media';
$verify_token='';
$callback_url = 'http://extraordinarymanicmachine.com/montigo/nongsa/instagram/callback.php';


//SETTING UP THE CURL SETTINGS...
$attachment =  array(
'client_id' => $client_id,
'client_secret' => $client_secret,
'object' => $object,
'aspect' => $aspect,
'verify_token' => $verify_token,
'callback_url'=>$callback_url
);

//URL TO THE INSTAGRAM API FUNCTION
$url = "https://api.instagram.com/v1/subscriptions/";

$ch = curl_init();

//EXECUTE THE CURL...
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
$result = curl_exec($ch);
curl_close ($ch);

//PRINT THE RESULTS OF THE SUBSCRIPTION, IF ALL GOES WELL YOU'LL SEE A 200
print_r($result);


?>