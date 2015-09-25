<?php
    /*
    Cette classe contient toutes les informations necessaire au bon fonctionnement d'1958.
    Les noms des tables de BDD, les points paccordés à chaque action
    */
    require_once 'core.php';

    Class DB
    {

        static $db;

        //tables

        function __construct ()
        {
            try {
                $bdd = new PDO('mysql:host=' . HOSTNAME . ';dbname=' . DBNAME, USER_DB, PASS_DB);
                $bdd->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Aide à l'erreur
                $utf8 = $bdd->query ('SET NAMES \'utf8\';');
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    // duplicate entry, do something else
                } else {
                    // an error other than duplicate entry occurred
                }
            }
            self::$db = $bdd;
        }

        static function formate_sql_date ($date)
        {
            $date_explode = explode ("-", $date);
            $jour = $date_explode[2];
            $mois = $date_explode[1];
            $annee = $date_explode[0];

            switch ($mois) {
                case 1:
                    $mois = "Jan";
                    break;
                case 2:
                    $mois = "Fev";
                    break;
                case 3:
                    $mois = "Mars";
                    break;
                case 4:
                    $mois = "Avr";
                    break;
                case 5:
                    $mois = "Mai";
                    break;
                case 6:
                    $mois = "Juin";
                    break;
                case 7:
                    $mois = "Juil";
                    break;
                case 8:
                    $mois = "Aout";
                    break;
                case 9:
                    $mois = "Sept";
                    break;
                case 10:
                    $mois = "Oct";
                    break;
                case 11:
                    $mois = "Nov";
                    break;
                case 12:
                    $mois = "Déc";
                    break;

                default:
                    # code...
                    break;
            }
            $annee = substr ($annee, -2);
            return $jour . " " . $mois . " " . $annee;
        }
    }

    $DB = new DB();

?>