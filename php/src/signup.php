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

if (!($_POST['username'] && $_POST['email'] && $_POST['password'])) {
    $code = 400;
    $message = 'Missing data';
    $header->statusCode = $code;
    $header->setHeader();
    // Log that data is missing and maybe what one is missing?
    echo json_encode(['code' => $code, 'message' => $message]);
    exit;
}

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// Check if user already exists
$stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$results = $stmt->get_result();

if ($results->num_rows) {
    $code = 403;
    $message = 'user already exists, try forgot password?';
    $header->statusCode = $code;
    $header->setHeader();
    // log no signup failure
    echo json_encode(['code' => $code, 'message' => $message]);
    exit;
}

$salt = createSalt();
$hash = createHash($password, $salt);
$userId = insertNewUser($mysqli, $username, $email, $hash);
$sessionKey = 'sessionId';

if (isset($_COOKIE[$sessionKey])) {
    setcookie($sessionKey, '', time() - 1000, '/');
}

$sessionId = uniqid('', true);

addSession($mysqli, $userId, $sessionId);

$header->setHeader();
setcookie('sessionId', $sessionId, time() + 300, '/');

// return username
echo json_encode(['username' => $username]);


/**
 * Helper function to insert a new user, returns the newly created user id
 * 
 * @param mysqli $mysqli The connection to the MySQL database
 * @param string $username The user's user name
 * @param string $email The email for the user
 * @param string $hash the user's hashed password
 * 
 * @return int The id for the newly created user record
 */
function insertNewUser(mysqli $mysqli, string $username, string $email, string $hash): int {
    $stmt = $mysqli->prepare('INSERT INTO users(username, email, password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $username, $email, $hash);
    $stmt->execute();

    return $mysqli->insert_id;
}

?>