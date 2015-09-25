<?php

    /**
 * Created by PhpStorm.
 * User: Personne
 * Date: 18/09/2015
 * Time: 13:17
 * @property  salt_hashid
 */
    require_once "core.php";
class User {

    // POINTS

    static $PTS_VOTE = 10;
    static $PTS_FAST_VOTE = 5;
    static $TIME_FAST_VOTE = 5000;
    static $TIME_COMBO = 15000;

    // FIN POINTS

    static $table = DB_TABLE_USERS;
    static $salt_hashid = "FUCKING_GENIUS";


    public $id;

    public $hashid;

    public $ip;

    public $created;

    public $last_seen;

    public $points;


    function __construct($idUser = NULL){
        if(!is_null($idUser)){
            //INIT
            $this->id = $idUser;
            $this->init();
        }
    }

    public function init () {
        if($data = $this->user_exists()){
            $this->id = $data['id_user'];
            $this->hashid = $data['hashid_user'];
            $this->ip = $data['ip_user'];
            $this->created = $data['created_user'];
            $this->last_seen = $data['last_seen_user'];
            $this->points = $data['points_user'];
        }
    }

    public function user_exists () {
        $req = DB::$db->query ('SELECT * FROM ' . self::$table . ' WHERE id_user = "' . $this->id . '" LIMIT 1');
        return $req->fetch();
    }

    public function save(){
        $req = "INSERT INTO ".self::$table."
                    (`ip_user`, `created_user`, `last_seen_user`)
                    VALUES (:ip,:created,:last_seen)";

        $query = DB::$db->prepare($req);
        $query->bindParam(':ip', $this->ip);
        $query->bindParam(':created', $this->created);
        $query->bindParam(':last_seen', $this->last_seen);
        $query->execute();

        $id_new_user = DB::$db->lastInsertId();
        $this->id = $id_new_user;

        $this->hashid = self::generate_hashid($this->id);

        $this->saveHashid();
    }

    public function saveHashid(){
        $req = "UPDATE ".self::$table." SET `hashid_user`=:hashid WHERE id_user = ".$this->id;

        $query = DB::$db->prepare($req);
        $query->bindParam(':hashid', $this->hashid);
        $query->execute();
    }

    public function decodeHashid(){
        $HASHIDS = new Hashids\Hashids(self::$salt_hashid, 255 , 'abcdefghij1234567890');
        return $HASHIDS->decode($this->hashid)[0];
    }

    public function getPoints($arrayActions = []){
        $arrayPoints = [
            'total' => 0,
            'list' => []
        ];
        foreach($arrayActions as $action){
            switch($action){
                case 'VOTE':
                    $arrayPoints['total'] += self::$PTS_VOTE;
                    $arrayPoints['list']['VOTE'] = self::$PTS_VOTE;
                    break;
                case 'FAST_VOTE':
                    $arrayPoints['total'] += self::$PTS_FAST_VOTE;
                    $arrayPoints['list']['FAST_VOTE'] = self::$PTS_FAST_VOTE;
                    break;
            }
        }
        $this->addPoints($arrayPoints);

        $arrayPoints['grand_total'] = $this->points;
        return $arrayPoints;
    }

    public function addPoints ($arrayPoints) {
        $req = "UPDATE ".self::$table." SET `points_user`= `points_user` + :points WHERE id_user = ".$this->id;

        $query = DB::$db->prepare($req);
        $query->bindParam(':points', $arrayPoints['total']);
        $query->execute();

        $this->points += $arrayPoints['total'];
    }

    static function generate_hashid ($id) {
        $HASHIDS = new Hashids\Hashids(self::$salt_hashid, 255 , 'abcdefghij1234567890');
        return $HASHIDS->encode($id);
    }
}