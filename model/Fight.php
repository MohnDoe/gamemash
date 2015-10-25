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
    public $GameLeft;

    public $id_game_right;
    public $GameRight;

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

    public function init ($data = null) {
        if(is_null($data)){
            if(!$data = $this->fight_exists()){
                return false;
            }
        }

        $this->id = $data['id_fight'];
        $this->id_game_left = $data['id_game_left'];
        //$this->GameLeft = new Game($this->id_game_left);
        $this->id_game_right = $data['id_game_right'];
        //$this->GameRight = new Game($this->id_game_right);
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

    public function fight_exists () {
        $req = DB::$db->query ('SELECT * FROM ' . self::$table . ' WHERE id_fight = "' . $this->id . '" LIMIT 1');
        return $req->fetch();
    }

    public function create_fight($GameLeft, $GameRight, $id_user){
        $this->id_game_left = $GameLeft->id;
        $this->GameLeft = $GameLeft;
        $this->id_game_right = $GameRight->id;
        $this->GameRight = $GameRight;
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

    public function generate_token () {
        /*
        $HASHIDS = new Hashids\Hashids(self::$salt_hashid, 64 , 'abcdefghij1234567890');
        $to_encode = $this->id_game_left.$this->id_game_right;
        $token = $HASHIDS->encode($to_encode);
        //*/
        return bin2hex(openssl_random_pseudo_bytes(20));
    }

    public function saveVote () {
        $req = "UPDATE ".self::$table." SET `date_voted_fight`=:date_voted,`id_game_winner`=:id_game_winner,`id_game_looser`=:id_game_looser,`is_done_fight`=1 WHERE id_fight = ".$this->id;

        $query = DB::$db->prepare($req);
        $query->bindParam(':date_voted', $this->date_voted);
        $query->bindParam(':id_game_winner', $this->id_game_winner);
        $query->bindParam(':id_game_looser', $this->id_game_looser);
        $query->execute();
    }

    public function convert_in_array(){
        $GameLeft = new Game($this->id_game_left);
        $GameRight = new Game($this->id_game_right);
        $is_winner = false;
        if($this->is_done){
            if($this->id_game_left == $this->id_game_winner){
                $is_winner = true;
                $GameWinner = $GameLeft;
                $GameLooser = $GameRight;
            }else if($this->id_game_right == $this->id_game_winner){
                $is_winner = true;
                $GameWinner = $GameRight;
                $GameLooser = $GameLeft;
            }
        }
        $result = [
            'id' => $this->id,
            'token' => $this->token,
            'date_created' => $this->date_created,
            'is_winner' => $is_winner,
            'is_done' => !!$this->is_done
        ];
        if($this->is_done && $is_winner){
            $result['game_winner'] = $GameWinner->convert_in_array();
            $result['game_looser'] = $GameLooser->convert_in_array();
        }else{
            $result['gameRight'] = $GameRight->convert_in_array();
            $result['gameLeft'] = $GameLeft->convert_in_array();
        }

        return $result;
    }

    static function get_statistics () {
        $req = DB::$db->query ('SELECT COUNT(*) AS result FROM ' . self::$table . ' WHERE is_done_fight = 1 LIMIT 1');
        $results = [];
        $data = $req->fetch();

        $results['nb_votes'] = $data['result'];

        return $results;
    }

    static function generate_fight($idUser){
        $GamesFight = Game::get_random_games(2);
        $GameLeft = $GamesFight[0];
        $GameRight = $GamesFight[1];

        //saving fight
        $Fight = new Fight();
        $Fight->create_fight($GameLeft,$GameRight, $idUser);
        $Fight->save();

        return $Fight;
    }

    static function get_fights_done($period = null, $limit = null)
    {
        $req = 'SELECT * FROM ' . self::$table;
        $req .= ' WHERE is_done_fight = 1';
        if(!is_null($period)){
            $req .= ' AND date_voted_fight > DATE_SUB(NOW(), INTERVAL '.$period.' DAY)';
        }
        $req .= ' ORDER BY date_voted_fight DESC';
        if(!is_null($limit)){
            $req .= ' LIMIT '.$limit;
        }

        $query = DB::$db->prepare($req);

        $query->execute();

        $results = array();

        while($data = $query->fetch()){
            $Fight = new Fight();
            $Fight->init($data);

            $results[] = $Fight->convert_in_array();
        }

        return $results;
    }
}