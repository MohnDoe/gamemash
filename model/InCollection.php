<?php

    /**
     * Created by PhpStorm.
     * User: Personne
     * Date: 04/11/2015
     * Time: 09:33
     */

    class InCollection
    {
        static $table = DB_TABLE_IN_COLLECTION;

        static function add_game($id_user, $id_game){
            //TODO : check if game is in collection first
            //FIXME : avoid adding game twice to a collection
            $req = "INSERT INTO ".self::$table."
                    (`id_user_in_collection`, `id_game_in_collection`, `created_at_in_collection`)
                    VALUES (:id_user,:id_game, NOW())";

            $query = DB::$db->prepare($req);
            $query->bindParam(':id_user', $id_user);
            $query->bindParam(':id_game', $id_game);
            $query->execute();

            //TODO : return false if problem or game already in collection
            return true;
        }

        //TODO : check if game is in collection of a user function
        //TODO : delete function
        //TODO : get whole collection of a user

    }