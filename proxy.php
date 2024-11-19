<?php

$target = [
    'scheme' => 'http', // The protocol to use (e.g., http, https)
    'host' => 'localhost', // The hostname or IP address of the target server
    'port' => 8000, // The port number on the target server
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); // You can adjust this to your specific situation.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_SUPPRESS_CONNECT_HEADERS, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
curl_setopt($ch, CURLOPT_URL, "{$target['scheme']}://{$target['host']}:{$target['port']}".$_SERVER['REQUEST_URI']);
curl_setopt($ch, CURLOPT_HTTPHEADER, (function () use ($target) {
    $headers = getallheaders();
    $headers['Host'] = $target['host'];
    unset($headers['Content-Length']);
    if (isset($headers['Content-Type'])) {
        $headers['Content-Type'] = preg_replace('/; boundary=[^;]+/', '', $headers['Content-Type']);
    }
    return array_map(function ($k, $v) { return "$k: $v"; }, array_keys($headers), array_values($headers));
})());
if (!empty($_FILES)) {
    $files = array_map(function ($v) { return curl_file_create($v['tmp_name'], $v['type'], $v['name']); }, $_FILES);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge($_POST, $files));
} elseif (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'PATCH'])) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

$result = curl_exec($ch);
if ($result === false) {
    http_response_code(500);
    echo curl_error($ch);
    curl_close($ch);
    exit;
}
curl_close($ch);

[$header, $body] = explode("\r\n\r\n", $result, 2);
foreach (explode("\r\n", $header) as $h) {
    if (preg_match('/^Transfer-Encoding:/', $h)) {
        continue;
    }
    header($h);
}
echo $body;
