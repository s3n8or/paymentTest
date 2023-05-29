<?php

/**
 * Takes in a hash and returns the salt for it
 * 
 * @param string $hash The hashed password with an embedded salt
 * 
 * @return string The salt that was embedded in the password
 */
function getSaltFromHash(string $hash): string {
    // return substr($hash, -16);
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

/**
 * Helper method for creating the salt for password hashing
 * 
 * @param int $length The length of the salt, due to how it is generated you should use an even number
 * 
 * @return string
 */
// function createSalt(int $length): string {
//     $salt = openssl_random_pseudo_bytes($length / 2);
function createSalt(): string {
    // Ideally this would be in a class where we'd have a const value of 16, I hard coded it so others would know where this number comes from
    $salt = openssl_random_pseudo_bytes(16 / 2);

    return bin2hex($salt);
}