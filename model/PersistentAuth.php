<?php

    /**
     * Created by PhpStorm.
     * User: Personne
     * Date: 11/10/2015
     * Time: 06:45
     */
    class PersistentAuth {
        // START CONFIG
        // Here are some config variables to help integrate this code

        // Database settings
        const SESSION_TABLE  = 'user_sessions';
        const USERID_COLUMN  = 'user_id';
        const SESSION_COLUMN = 'session';
        const CREATED_COLUMN = 'created';

        // Session settings
        const SESSION_KEY = 'PersistentAuthCookie';

        // Cron settings
        const USE_CRON = false;

        // Cookie Settings
        const COOKIE_KEY = 'PersistentAuthCookie';
        const NUM_DAYS = 30; // number of key is valid
        // END CONFIG

        public static $userId = null;
        public static $sessionKey = null;

        /**
         * Set a cookie for this user id
         *
         * @param type $userId
         */
        public static function login($userId) {
            $key = self::generateKey();

            $db = self::getDb();

            $stmt = $db->prepare('INSERT INTO ' .
                                 PersistentAuth::SESSION_TABLE . '(' .
                                 PersistentAuth::USERID_COLUMN . ', ' .
                                 PersistentAuth::SESSION_COLUMN . ') VALUES (:user_id,
            :session_key)');

            $stmt->bindParam(':user_id',     $userId);
            $stmt->bindParam(':session_key', $key);

            // Store key in database
            $stmt->execute();

            // Store key in cookie
            setcookie(self::COOKIE_KEY,
                      "$userId|$key",
                      time() + (86400 * self::NUM_DAYS),
                      '/');
        }

        /**
         * Remove the cookie so they can't cookie login
         */
        public static function logout() {
            setcookie(self::COOKIE_KEY,
                      self::$userId . '|' . self::$sessionKey,
                      time() - 3600,
                      '/');
        }

        /**
         * Checks if the cookie has a valid session. Return true if it does
         * return false if it doesn't
         *
         * @return boolean whether or not the cookie had a valid session
         */
        public static function cookieLogin() {
            if (is_null(self::$sessionKey)) self::loadSession();

            // No cookie at all
            if (!self::$sessionKey || !self::$userId) return false;

            $db = self::getDb();

            // check if cookie row is found
            $stmt = $db->prepare('SELECT ' . PersistentAuth::USERID_COLUMN .
                                 ' FROM ' . PersistentAuth::SESSION_TABLE . ' WHERE
            ' . PersistentAuth::USERID_COLUMN . ' = :user_id AND ' .
                                 PersistentAuth::SESSION_COLUMN . ' = :session LIMIT 1');

            $stmt->bindParam(':user_id',   self::$userId);
            $stmt->bindParam(':session',   self::$sessionKey);
            $stmt->execute();

            if ($data = $stmt->fetch()) {
                $_SESSION[PersistentAuth::SESSION_KEY] = true;
                return self::$userId;
            } else {
                return false;
            }
        }

        /**
         * Check if we were logged in via cookie
         *
         * @return bool
         */
        public static function isCookieLogin() {
            if (is_null(self::$sessionKey)) self::loadSession();

            return isset($_SESSION[PersistentAuth::SESSION_KEY])
            && $_SESSION[PersistentAuth::SESSION_KEY];
        }

        /**
         * generate 128-bit random number (39 characters long)
         *
         * @return string
         */
        protected static function generateKey() {
            // get the largest 999999999 we can rand to.
            $max = (int)str_pad('', strlen(mt_getrandmax()) - 1, 9);
            $min = (int)str_pad('1', strlen($max), 0, STR_PAD_RIGHT);

            $key = '';

            while (strlen($key) < 39) {
                $key .= mt_rand($min, $max);
            }

            return substr($key, 0, 39);
        }

        /**
         * Run this function on a cron to save user time. This will delete
         * any session that is older than the NUM_DAYS set above
         */
        public static function deleteOld() {
            // Delete old sessions
            $stmt = DB::$db->prepare('DELETE FROM ' .
                                 PersistentAuth::SESSION_TABLE . ' WHERE
            ' . PersistentAuth::CREATED_COLUMN . '< :timeout LIMIT 1');

            $timeout = strtotime('-' . PersistentAuth::NUM_DAYS . ' days');
            $date =  date('Y-m-d H:i:s', $timeout);
            $stmt->bindParam(':timeout', $date);

            $stmt->execute();
        }

        /**
         *
         */
        protected static function loadSession() {
            $db = self::getDb();
            if (!PersistentAuth::USE_CRON) {
                self::deleteOld();
            }

            // load cookie
            if (isset($_COOKIE[PersistentAuth::COOKIE_KEY])) {
                $parts = explode('|', $_COOKIE[PersistentAuth::COOKIE_KEY]);

                self::$userId     = $parts[0];
                self::$sessionKey = $parts[1];
            } else {
                self::$userId = self::$sessionKey = false;
            }
        }

        /**
         * Function to get our database connection.
         *
         * TODO change this to fill your needs
         *
         * @global type $db
         * @return PDO $db
         */
        protected static function getDb() {
            return DB::$db;
        }
    }