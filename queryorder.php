<?php
// Generate nonce string
$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$nonce = '';
for($i=1; $i <= 32; $i++)
{
    $pos = mt_rand(0, strlen($chars) - 1);
    $char = $chars[$pos];
    $nonce .= $char;
}
$binance_pay = "aalshnxgqtgaimxsa5wf9rdgcwkpcmts5i8ztalv9x3psjvzhx1ogcz673gwhtz0";
$binance_pay_secret = "pmerozghjg7lfsbovjd1twwiteouwdcyefq0szgo6uzsldpaxvr0ik7xlvaameq0";

$ch = curl_init();
$timestamp = round(microtime(true) * 1000);
// Request body
$request = array(
    "merchantTradeNo" => "2223",
);

$json_request = json_encode($request);
$payload = $timestamp."\n".$nonce."\n".$json_request."\n";
$signature = strtoupper(hash_hmac('SHA512',$payload,$binance_pay_secret));

echo $timestamp."<br/>";
echo $signature."<br/>";

$headers = array();
$headers[] = "Content-Type: application/json";
$headers[] = "BinancePay-Timestamp: $timestamp";
$headers[] = "BinancePay-Nonce: $nonce";
$headers[] = "BinancePay-Certificate-SN: $binance_pay";
$headers[] = "BinancePay-Signature: $signature";

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_URL, "https://bpay.binanceapi.com/binancepay/openapi/v2/order/query");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_request);

$result = curl_exec($ch);
if (curl_errno($ch)) { echo 'Error:' . curl_error($ch); }
echo $result;
curl_close ($ch);
