<?php
$token = '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';
$url = 'https://api.github.com/rate_limit';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: token ' . $token,
    'User-Agent: PHP-cURL-request'
]);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    $data = json_decode($response, true);
    print_r($data);
}
curl_close($ch);
?>