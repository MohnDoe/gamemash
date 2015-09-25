<?php
/**
 * Created by PhpStorm.
 * User: Personne
 * Date: 18/09/2015
 * Time: 01:20
 */

class Fight {

    static $table = DB_TABLE_FIGHTS;
    static $salt_hashid = "THIS_FIGHT_IS_A_LIE_MATHAFUCKAAAAAA";

    public $id;

    public $id_game_left;

    public $id_game_right;

    public $elo_game_left_before;

    public $elo_game_right_before;

    public $date_created;

    public $date_voted;

    public $id_game_winner;

    public $id_game_looser;

    public $token;

    public $is_done = 0;

    public $id_user;


    function __construct($idFight = NULL){
        if(!is_null($idFight)){
            // INIT
            $this->id = $idFight;
            $this->init();
        }
    }

    public function init () {
        if($data = $this->fight_exists()){
            $this->id = $data['id_fight'];
            $this->id_game_left = $data['id_game_left'];
            $this->id_game_right = $data['id_game_right'];
            $this->elo_game_left_before = $data['elo_game_left_before'];
            $this->elo_game_right_before = $data['elo_game_right_before'];
            $this->date_created = $data['date_created_fight'];
            $this->date_voted = $data['date_voted_fight'];
            $this->id_game_winner = $data['id_game_winner'];
            $this->id_game_looser = $data['id_game_looser'];
            $this->token = $data['token_fight'];
            $this->is_done = $data['is_done_fight'];
            $this->id_user = $data['id_user_fight'];
        }
    }

    public function fight_exists () {
        $req = DB::$db->query ('SELECT * FROM ' . self::$table . ' WHERE id_fight = "' . $this->id . '" LIMIT 1');
        return $req->fetch();
    }

    public function create_fight($GameLeft, $GameRight, $id_user){
        $this->id_game_left = $GameLeft->id;
        $this->id_game_right = $GameRight->id;
        $this->elo_game_left_before = $GameLeft->current_elo;
        $this->elo_game_right_before = $GameRight->current_elo;
        $this->date_created = date("Y-m-d H:i:s");

        $this->token = $this->generate_token();

        $this->id_user = $id_user;
    }

    public function save(){
        $req = "INSERT IGNORE INTO ".self::$table."
                    (`id_game_left`, `id_game_right`, `elo_game_left_before`, `elo_game_right_before`, `date_created_fight`, `token_fight`, `id_user_fight`)
                    VALUES (:id_game_left,:id_game_right,:elo_game_left_before,:elo_game_right_before,:date_created,:token,:id_user)";

        $query = DB::$db->prepare($req);
        $query->bindParam(':id_game_left', $this->id_game_left);
        $query->bindParam(':id_game_right', $this->id_game_right);
        $query->bindParam(':elo_game_left_before', $this->elo_game_left_before);
        $query->bindParam(':elo_game_right_before', $this->elo_game_right_before);
        $query->bindParam(':date_created', $this->date_created);
        $query->bindParam(':token', $this->token);
        $query->bindParam(':id_user', $this->id_user);
        $query->execute();

        $id_new_fight = DB::$db->lastInsertId();
        $this->id = $id_new_fight;
    }

    public function set_vote($side){
        $this->date_voted = date("Y-m-d H:i:s");
        if($side == 'left'){
            $this->id_game_winner = $this->id_game_left;
            $this->id_game_looser = $this->id_game_right;
        }else if($side == 'right'){
            $this->id_game_winner = $this->id_game_right;
            $this->id_game_looser = $this->id_game_left;
        }else{
            $this->id_game_winner = 0;
            $this->id_game_looser = 0;
        }
        $this->is_done = 1;
    }

    private function generate_token () {
        $HASHIDS = new Hashids\Hashids(self::$salt_hashid, 255 , 'abcdefghij1234567890');
        $to_encode = rand()*rand()+rand();
        $token = $HASHIDS->encode($to_encode);
        return $token;
    }

    public function saveVote () {
        $req = "UPDATE ".self::$table." SET `date_voted_fight`=:date_voted,`id_game_winner`=:id_game_winner,`id_game_looser`=:id_game_looser,`is_done_fight`=1 WHERE id_fight = ".$this->id;

        $query = DB::$db->prepare($req);
        $query->bindParam(':date_voted', $this->date_voted);
        $query->bindParam(':id_game_winner', $this->id_game_winner);
        $query->bindParam(':id_game_looser', $this->id_game_looser);
        $query->execute();
    }
}