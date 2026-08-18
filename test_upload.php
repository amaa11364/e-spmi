<?php
$boundary = "----WebKitFormBoundary7MA4YWxkTrZu0gW";
$content = "--$boundary\r\n";
$content .= "Content-Disposition: form-data; name=\"files[]\"; filename=\"test1.txt\"\r\n";
$content .= "Content-Type: text/plain\r\n\r\n";
$content .= "Hello World 1\r\n";
$content .= "--$boundary\r\n";
$content .= "Content-Disposition: form-data; name=\"files[]\"; filename=\"test2.txt\"\r\n";
$content .= "Content-Type: text/plain\r\n\r\n";
$content .= "Hello World 2\r\n";
$content .= "--$boundary\r\n";
$content .= "Content-Disposition: form-data; name=\"is_public\"\r\n\r\n";
$content .= "1\r\n";
$content .= "--$boundary--\r\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/dokumen/folders/1/files");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;
