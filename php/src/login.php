<?php

require_once('configs.php');
require_once('Header.php');
require_once('database.php');
require_once('session.php');
require_once('password.php');

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
    echo json_encode(['code' => $code, 'message' => $message]);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];

// look up user
$stmt = $mysqli->prepare('SELECT id, username, password FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$results = $stmt->get_result();

if (!$results) {
    $code = 500;
    $message = 'Server issues, contact admin';
    $header->statusCode = $code;
    $header->setHeader();
    // log no results failure
    echo json_encode(['code' => $code, 'message' => $message]);
    exit;
}

$user = $results->fetch_object();

if (!$user) {
    $code = 404;
    $message = 'User not found';
    $header->statusCode = $code;
    $header->setHeader();
    // log user not found
    echo json_encode(['code' => $code, 'message' => $message]);
    exit;
}

$userId = $user->id;
$username = $user->username;
$hashedPassword = $user->password;
$salt = getSaltFromHash($hashedPassword);

$hash = createHash($password, $salt);

if ($hash !== $hashedPassword) {
    $code = 401;
    $message = 'Wrong credentials';
    $header->statusCode = $code;
    $header->setHeader();
    // log message
    echo json_encode(['code' => $code, 'message' => $message]);
    exit;
}

$sessionId = uniqid('', true);

// Attempt to add the session. Because there's a unique constraint on userId, if the user happens to already have a session this will fail. So if it fails, cleanup the session entry and try again.
if (addSession($mysqli, $userId, $sessionId) !== true) {
    if (deleteSession($mysqli, $userId) !== true) {
        $code = 500;
        $message = 'Error creating session: ' . $mysqli->error;
        $header->statusCode = $code;
        $header->setHeader();
        // log error
        echo json_encode(['code' => $code, 'message' => $message]);
        exit;
    }
    
    if (addSession($mysqli, $userId, $sessionId) !== true) {
        $code = 500;
        $message = 'Error creating session: ' . $mysqli->error;
        $header->statusCode = $code;
        $header->setHeader();
        // log error
        echo json_encode(['code' => $code, 'message' => $message]);
        exit;
    }
}

$header->setHeader();
setcookie('sessionId', $sessionId, time() + 300, '/');

// return username
echo json_encode(['username' => $username]);

?>
