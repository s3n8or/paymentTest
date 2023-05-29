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
    echo json_encode(['code' => $code, 'message' => $message]);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];
// $email = 'jon@jontrent.net';
// $password = 'whattheheckman';

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


/**
 * Convenience method to add a session for the user
 * 
 * @param mysqli $mysqli An instance of the MySQL database connection
 * @param int $userId Id of the user we're saving the sessionId for
 * @param string $sessionId The sessionId we're saving for the user
 * 
 * @return mysqli_result|bool This should always return a bool
 */
function addSession(mysqli $mysqli, int $userId, string $sessionId): mysqli_result|bool {
    $sql = "INSERT INTO sessions(sessionId, userId, createdAt) VALUES ('{$sessionId}', {$userId}, NOW())";

    return $mysqli->query($sql);
}

/**
 * Convenience method to delete a session for the user
 * 
 * @param mysqli $mysqli An instance of the MySQL database connection
 * @param string $sessionId The sessionId we're saving for the user
 * 
 * @return mysqli_result|bool This should always return a bool
 */
function deleteSession(mysqli $mysqli, int $userId): mysqli_result|bool {
    $sql = "DELETE FROM sessions WHERE userId = {$userId}";

    return $mysqli->query($sql);
}
/**
 * 
 */
function getUsers(mysqli $mysqli): array {
    $sql = 'SELECT * FROM users';
    $users = [];

    if ($result = $mysqli->query($sql)) {
        while ($data = $result->fetch_object()) {
            $users[] = $data;
        }
    }

    return $users;
}

/**
 * Helper method for creating the salt for password hashing
 * 
 * @param int $length The length of the salt, due to how it is generated you should use an even number
 * 
 * @return string
 */
function createSalt(int $length): string {
    $salt = openssl_random_pseudo_bytes($length / 2);

    return bin2hex($salt);
}

/**
 * Takes in a hash and returns the salt for it
 * 
 * @param string $hash The hashed password with an embedded salt
 * 
 * @return string The salt that was embedded in the password
 */
function getSaltFromHash(string $hash): string {
    return substr($hash, -16);
}

/**
 * Given a plain text password and a salt, it will create a hashed passwork with the salt, and embed the salt in the password
 * 
 * @param string $password The un-hashed password
 * @param string $salt The salt to use for the password
 * 
 * @return string the hashed password
 */
function createHash(string $password, string $salt): string {
    return hash('sha256', $password . $salt) . $salt;
}

?>
