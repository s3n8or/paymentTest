<?php

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