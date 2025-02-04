<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    return;
}
$ipAddress = $_SERVER['REMOTE_ADDR'];

$json = file_get_contents('php://input');
$data = json_decode($json, true);
if ($data === null) {
    logMessage('missing post keys', $ipAddress,'*','*');
    sendResponseAndExit('missing post keys');
}

$key = htmlspecialchars($data['key'] ?? "");
$hardwareKey = htmlspecialchars($data['hardwareKey'] ?? "");

if ($key == ""  || $hardwareKey == "") {
    logMessage('missing post key', $ipAddress,$key,$hardwareKey);
    sendResponseAndExit('missing post key');
}

$fileName = '../keys/' . $key . '.json';

if (!file_exists($fileName)) {
    logMessage('key not found', $ipAddress,$key,$hardwareKey);
    sendResponseAndExit('key not found');
}
$fileContent = file_get_contents($fileName);
$jsonData = json_decode($fileContent, true);

$currentDate = new DateTime();

$keyParts = explode('-', $key);
$expireDate = new DateTime(sprintf('2025-%s-%s',$keyParts[0],$keyParts[1]));
if ($currentDate > $expireDate)
{
    logMessage('date is expired', $ipAddress,$key,$hardwareKey);
    updateKey('date is expired', $fileName, $jsonData,'date is expired');
    sendResponseAndExit('date is expired');
}

if (!isset($jsonData['hardwareKey'])) {
    $jsonData['hardwareKey'] = $hardwareKey;
    logMessage('loggedin new hardware', $ipAddress,$key,$hardwareKey);
    updateKey('true', $fileName, $jsonData,'loggedin');
    sendResponseAndExit('true');
}

if ($jsonData['hardwareKey'] === $hardwareKey) {
    logMessage('loggedin', $ipAddress,$key,$hardwareKey);
    updateKey('true', $fileName, $jsonData,'loggedin');
    sendResponseAndExit('true');
}

logMessage('hardware key not found', $ipAddress,$key,$hardwareKey);
updateKey('hardware key not found', $fileName, $jsonData,'hardware key not found');
sendResponseAndExit('hardware key not found');

exit();

function updateKey(string $response, string $fileName, array $jsonData, string $statusLog): void
{
    $currentDate = new DateTime();
    $jsonData['lastLogin'] = sprintf("%s - %s",$currentDate->format('Y-m-d H:i:s'), $statusLog);

    $jsonData = json_encode($jsonData, JSON_PRETTY_PRINT);
    file_put_contents($fileName, $jsonData);
}

function logMessage(string $statusLog, string $ip, string $key, string $hardwareKey, ): void
{
    $currentDate = new DateTime();
    $file = '../logs/log.csv';
    $content = sprintf("%s;%s;%s;%s;%s\n", $currentDate->format('Y-m-d H:i:s'),$statusLog, $ip,$key,$hardwareKey);

    file_put_contents($file, $content, FILE_APPEND);
}

function sendResponseAndExit(string $response)
{
    echo sprintf('{"result" : "%s"}', $response);
    exit();
}