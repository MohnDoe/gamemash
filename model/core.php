<?php
/**
 * Created by PhpStorm.
 * User: Personne
 * Date: 25/08/2015
 * Time: 23:09
 */

    function dateTimeToMilliseconds(\DateTime $dateTime)
    {
        $secs = $dateTime->getTimestamp(); // Gets the seconds
        $millisecs = $secs*1000; // Converted to milliseconds
        $millisecs += $dateTime->format("u")/1000; // Microseconds converted to seconds
        return $millisecs;
    }

    session_start ();

    header('Content-Type: text/html; charset=utf-8');

    ini_set('xdebug.var_display_max_depth', 5);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 1024);
    header ('Content-Type: text/html; charset=UTF-8');

    DEFINE('RATIO_POP_MULTIPLI', 0.99519804434435373165003884241728);

    // connexion db
    if ($_SERVER["REMOTE_ADDR"] != "127.0.0.1") {
        // on heroku
        DEFINE('URL_BASE_HREF', '/');
        // database
        $urlClearDB = parse_url (getenv ("CLEARDB_DATABASE_URL"));
        DEFINE('HOSTNAME', $urlClearDB["host"]);
        DEFINE('DBNAME', substr ($urlClearDB["path"], 1));
        DEFINE('USER_DB', $urlClearDB["user"]);
        DEFINE('PASS_DB', $urlClearDB["pass"]);

        DEFINE('DOCUMENT_ROOT', $_SERVER["DOCUMENT_ROOT"]."/");
    } else {
        DEFINE('URL_BASE_HREF', '/gamemash2/');
        DEFINE('HOSTNAME', 'localhost');
        DEFINE('DBNAME', 'db_gamemash');
        DEFINE('USER_DB', 'root');
        DEFINE('PASS_DB', '');

        DEFINE('DOCUMENT_ROOT', $_SERVER["DOCUMENT_ROOT"]."/gamemash2");
    }

    DEFINE('GIANTBOMB_API_KEY', "82f5dd7211eacfd3b0bd03ed564531a4ef1fe3c5");

    DEFINE('DB_TABLE_GAMES', 'games');
    DEFINE('DB_TABLE_FIGHTS', 'fights');
    DEFINE('DB_TABLE_USERS', 'users');
    DEFINE('DB_TABLE_PLATFORMS', 'platforms');
    DEFINE('DB_TABLE_IS_IN_PLATFORM', 'is_in_platform');

    /*
     * REQUIRING ALL CLASSES
     */
    require_once 'DB.php';
    require_once 'Game.php';
    require_once 'User.php';
    require_once 'Level.php';
    require_once 'Fight.php';
    require_once 'Platform.php';
    require_once 'ELORanking.php';
    require_once 'GiantBomb/GiantBomb.php';
    require_once $_SERVER['DOCUMENT_ROOT'].URL_BASE_HREF.'vendor/autoload.php';