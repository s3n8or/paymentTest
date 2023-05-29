<?php

require_once('configs.php');
require_once('Header.php');
require_once('database.php');

$header = new Header();
$configs = getConfigs();
$mysqli;

try {
    $mysqli = getMysqli($configs);
} catch (Exception $e) {
    $code = 500;
    $message = 'Server issues, contact admin';
    $header->statusCode = $code;
    $header->setHeader();
    // log out $e->getMessage()
    echo json_encode(['code' => $code, 'error' => $message]);
    exit;
}

$cookieKey = 'sessionId';
$response = json_encode(['code' => 203, 'message' => 'logged out']);
$header->statusCode = 203;
$header->setHeader();

// get cookie
if (!isset($_COOKIE[$cookieKey])) {
    echo $response;
    exit;
}

$sessionId = $_COOKIE[$cookieKey];

// delete cookie table entry
$sql = "DELETE FROM sessions WHERE sessionId = '{$sessionId}'";
$mysqli->query($sql);

// delete cookie
setcookie($cookieKey, '', time() - 1000);

// return 203
echo $response;

?>
