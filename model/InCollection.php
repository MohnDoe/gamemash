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
            if(self::check_in_collection($id_user, $id_game)){
                self::delete_in_collection($id_user, $id_game);
                return false;
            }else{
                $req = "INSERT INTO ".self::$table."
                    (`id_user_in_collection`, `id_game_in_collection`, `created_at_in_collection`)
                    VALUES (:id_user,:id_game, NOW())";

                $query = DB::$db->prepare($req);
                $query->bindParam(':id_user', $id_user);
                $query->bindParam(':id_game', $id_game);
                $query->execute();

                return true;
            }
        }

        static function check_in_collection($id_user, $id_game){
            $req = 'SELECT * FROM ' . self::$table . ' WHERE id_user_in_collection = :id_user AND id_game_in_collection = :id_game LIMIT 1';
            $query = DB::$db->prepare($req);
            $query->bindParam(':id_user', $id_user);
            $query->bindParam(':id_game', $id_game);
            $query->execute();
            if($query->fetch()){
                return true;
            }else{
                return false;
            }
        }

        static function delete_in_collection($id_user, $id_game){
            $req = 'DELETE FROM ' . self::$table . ' WHERE id_user_in_collection = :id_user AND id_game_in_collection = :id_game';
            $query = DB::$db->prepare($req);
            $query->bindParam(':id_user', $id_user);
            $query->bindParam(':id_game', $id_game);
            $query->execute();
        }

        static function get_whole_user_collection($id_user , $limit = 10){
            $req = 'SELECT * FROM ' . self::$table . ' WHERE id_user_in_collection = :id_user AND id_game_in_collection = :id_game LIMIT '.$limit;
            $query = DB::$db->prepare($req);
            $query->bindParam(':id_user', $id_user);
            $query->bindParam(':id_game', $id_game);
            $query->execute();

            $results = array();
            while($data = $query->fetch()){
                $Game =  new Game();
                $Game->init($data);
                $results[] = $Game;
            }
            return $results;
        }

    }