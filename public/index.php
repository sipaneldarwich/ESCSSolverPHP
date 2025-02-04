<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    return;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);
if ($data !== null) {
    $key = htmlspecialchars($data['key'] ?? "");
    $hardwareKey = htmlspecialchars($data['hardwareKey'] ?? "");

    if ($key == ""  || $hardwareKey == "") {
        echo sprintf('{"result" : "%s"}', 'wrong key');
        return;
    }
} else {
    echo sprintf('{"result" : "%s"}', 'wrong keys');
    return;
}

$fileName = '../keys/' . $key . '.json';

$fileContent = file_get_contents($fileName);
if ($fileContent === false) {
    echo sprintf('{"result" : "%s"}', 'key not found');
    return;
}
$data = json_decode($fileContent, true);

$currentDate = new DateTime();
$expireDate = new DateTime($data['expireDate']);
$data['lastLogin'] = $currentDate->format('Y-m-d H:i:s');

if ($currentDate > $expireDate)
{
    echo sprintf('{"result" : "%s"}', 'date is expired');
    return;
}

if (!isset($data['hardwareKey'])) {
    $data['hardwareKey'] = $hardwareKey;
    echo sprintf('{"result" : "%s"}', 'true');
    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($fileName, $jsonData);
    return;
}

if ($data['hardwareKey'] === $hardwareKey) {
    echo sprintf('{"result" : "%s"}', 'true');
    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($fileName, $jsonData);
    return;
}

echo sprintf('{"result" : "%s"}', 'user not found');

return;