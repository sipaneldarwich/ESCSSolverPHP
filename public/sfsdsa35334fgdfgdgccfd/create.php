<?php
return;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if ($data !== null) {
        if (!isset($data['user']) || !isset($data['expireDate'])) {
            echo "Error: Invalid JSON data.";
            return;
        }
        $jsonData = json_encode($data['expireDate'], JSON_PRETTY_PRINT);

        $fileName = '../../keys/' . $data['user'] . '.json';
        if (file_exists($fileName)) {
            echo "Error: User already exists.";
            return;
        }
        umask(0777);

        $result = file_put_contents($fileName, $jsonData);
        chmod($fileName, 0644);
        if ($result === false) {
            echo "Error: Failed to write data to file.";
        } else {
            chmod('/'.$filename, 0777);
            echo "Success: Data has been written";
        }
    } else {
        echo "Error: Invalid JSON data.";
    }
} else {
    echo "Error: Request method is not POST.";
}