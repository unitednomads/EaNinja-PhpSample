<?php
require_once('../config.php');
if(!$_GET['uid']){
  echo "Set uid in query param!"; exit;
}

// Make an auth header
$jwtHeader = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
$jwtHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtHeader));
$jwtPayload = json_encode(['iss' => EANINJA_API_KEY, 'exp' => time() + 24*60*60]);
$jwtPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtPayload));
$jwtSignature = hash_hmac('sha256', $jwtHeader . "." . $jwtPayload, EANINJA_API_SECRET, true);
$jwtSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtSignature));
$jwt = $jwtHeader . "." . $jwtPayload . "." . $jwtSignature;
$authHeader = 'Authorization: Bearer '.$jwt;

// Make a POST request
$ch = curl_init();
$url = BASE_URL . '/ac/products/' . PRODUCT_ID . '/whitelisted_auth_keys/' . $_GET['uid'];
//echo $url; exit;


curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authHeader]);

$res = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close ($ch);

// Render result
echo '<pre>';
echo 'HTTP STATUS: '.$status."\n"; //must be 200; otherwise handle as an exception
echo '</pre>';
echo '<pre>';
echo $res; //parse and use this data
echo '</pre>';