<?php

$token = 'EAAvOBx4MMvUBOZBSfIuZCVFvZBbw24VWahw5ITCFr8pZBfxBehtDbM96t8E4fXStPjx0Fi0Hlt56zflflDfhgsEZBTLYbiv8NFEQjA3Q4nTXGU5BkstdZC38OUcUHHbF9sV08vgkNj9D2QPtupZC5okRvZBoZBo3VuD5X345doxmaLhly4UYmTRNt0iTQYy2Jse4sejP4z1BxSzADSq5feiO904yKYusZD';

$telefono = '573132432855';

$url = 'https://graph.facebook.com/v19.0/342492888949890/messages';

$mensaje = json_encode(array(
    "messaging_product" => "whatsapp",
    "to" => $telefono,
    "type" => "template",
    "template" => array(
        "name" => "hello_world",
        "language" => array(
            "code" => "en_US"
        )
    )
));

$headers = array(
    "Authorization: Bearer " . $token,
    "Content-Type: application/json"
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $mensaje);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

echo "Response: " . $response . "\n";
echo "Status Code: " . $status_code . "\n";

?>
