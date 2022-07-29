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
$binance_pay = "***yourbinancepay***";
$binance_pay_secret = "***yourbinancepaysecret***";

$ch = curl_init();
$timestamp = round(microtime(true) * 1000);
// Request body
$request = array(
    "env"=>array("terminalType"=>"MINI_PROGRAM"),
    "merchantTradeNo"=>"2224",
    "orderAmount"=> 0.10,
    "currency"=>"USDT",
    "goods"=>
    array(
       "goodsType"=>"01",
       "goodsCategory"=>"0000",
       "referenceGoodsId"=>"abc001",
       "goodsName"=>"apple",
       "goodsUnitAmount"=>array("currency"=>"USDT","amount"=>1.00)
      ),
    "shipping"=>array("shippingName"=>array("firstName"=>"Joe","lastName"=>"Don"),
    "shippingAddress"=>array("region"=>"NZ")),
    "buyer"=>array("buyerName"=>array("firstName"=>"cz","lastName"=>"zhao")
    )
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
curl_setopt($ch, CURLOPT_URL, "https://bpay.binanceapi.com/binancepay/openapi/v2/order");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_request);

$result = curl_exec($ch);
if (curl_errno($ch)) { echo 'Error:' . curl_error($ch); }
echo $result;
curl_close ($ch);
