<?php

namespace App;

session_start([
    'cookie_lifetime' => 86400
]);

/**
 * Responisble for retrieving data from `users` table and check whether an user is logged
 */
class User
{
    /**
     * Finds user by username
     * 
     * @param string $username 
     * @return array|boolean - User data
     */
    public static function getUserByUsername($username) {
        $pdo = Component\DBConnection::getConnection();

        $sql = 'SELECT `user_id`, `username`, `pass`
                FROM `users` WHERE `username` = ?';

        $stmt = $pdo->prepare($sql);

        $stmt->execute([$username]);

        return $stmt->fetch();
    }

    /**
     * Checks if user is logged or not
     * 
     * @return bool
     */
    public static function IsLogged(): bool
    {
        return $_SESSION['isLogged'] ?? false;
    }
}
