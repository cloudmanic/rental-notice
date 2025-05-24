<?php

$user = 'spicer@cloudmanic.com';
$pass = 'D********01';

$did = '19712640170'; // your SMS‑enabled DID in E.164 (e.g. “1” + area + number)
$dst = '15034510062'; // recipient number in E.164
$message = 'Hello from VoIP.ms API!'; // your message text

// $method = "sendSMS";
$method = 'getSMS'; // API method to send SMS

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "https://voip.ms/api/v1/rest.php?type=1&api_username={$user}&api_password={$pass}&method={$method}&did={$did}&dst={$dst}&message=".urlencode($message));
$result = curl_exec($ch);
curl_close($ch);

echo $result;

$response = json_decode($result, true);

// /* Get Errors - Invalid_Client */
// if ($response[status] != 'success') {
//     echo $response[status];
//     exit;
// }

// /* See if Password is Correct */
// $client = $response[clients][0];
// if ($password != $client[password]) {
//     echo "invalid_password";
//     exit;
// }

// /* Client Exists and Password OK */
// echo "{$client[client]} - {$client[firstname]} {$client[lastname]}";

// // VoIP.ms API endpoint
// $apiUrl = 'https://voip.ms/api/v1/rest.php';

// // your VoIP.ms credentials
// $data = [
// 'api_username' => 'spicer@cloudmanic.com',
// 'api_password' => 'DhhcDhhc01',

// // API method and parameters
// 'method' => 'sendSMS',
// 'did' => '19712640170', // your SMS‑enabled DID in E.164 (e.g. “1” + area + number)
// 'dst' => '15034510062', // recipient number in E.164
// 'message' => 'Hello from VoIP.ms API!', // your message text
// ];

// // build POST request
// $ch = curl_init($apiUrl);
// curl_setopt($ch, CURLOPT_POST, true);
// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// // execute and grab the response
// $response = curl_exec($ch);
// if (curl_errno($ch)) {
// throw new Exception('cURL error: ' . curl_error($ch));
// }
// curl_close($ch);

// echo "Response: " . $response . "\n";

// // parse and inspect
// $result = json_decode($response, true);
// if ($result['status'] === 'success') {
// echo "SMS sent! Message ID: " . $result['message_id'];
// } else {
// echo "Failed to send SMS: " . $result['status'] . ' – ' . $result['sms_credits'];
// }
