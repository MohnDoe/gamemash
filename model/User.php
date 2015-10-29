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

    static $PTS_VOTE = PTS_FOR_A_VOTE;
    static $PTS_VOTE_NONE = PTS_FOR_A_VOTE_NONE;
    static $PTS_FAST_VOTE = PTS_FOR_A_FAST_VOTE;
    static $TIME_FAST_VOTE = TIME_FOR_A__FAST_VOTE;
    static $TIME_COMBO = TIME_FOR_A_COMBO;

    // FIN POINTS

    static $table = DB_TABLE_USERS;
    static $salt_hashid = "FUCKING_GENIUS";

    static $salt_password = "WujtT7SXDx3y2vb8vk8wVfZN4xdwyDqeYyjeNTgcXWSGeXahjgJPXSdMhLf7kTX2";

    public $id;

    public $hashid;

    public $ip;

    public $created;

    public $last_seen;

    public $points = 0;

    public $name = "";

    public $is_registered = 0;

    public $register_at =  null;

    public $hash = "";

    public $email = "";

    public $status = 'not connected';

    static $array_characters = array(
        "Scorpion",
        "Sub-Zero",
        "Razor Callahan",
        "Mr. Domino",
        "Zelda",
        "Zangief",
        "Yoda",
        "Wrong Way",
        "Wong Who",
        "Wolverine",
        "Wolfgang Krauser",
        "White Mike",
        "Tony Hawk",
        "Tommy Vercetti",
        "Tommy",
        "Tomko",
        "Tom",
        "Tiger Woods",
        "Thi Barrett",
        "The Burger King",
        "The Amazon",
        "Weighted Companion Cube",
        "John MacTavish",
        "Wario",
        "Voldo",
        "Victor Zakhaev",
        "Victor Vance",
        "Abigail",
        "Vega",
        "Ulala",
        "Ty",
        "Trevor Belmont",
        "Terry Bogard",
        "Two.P",
        "A.J. Styles",
        "Abyss",
        "Adolf Hitler",
        "Afro Thunder",
        "Tarma Roving",
        "Aggro Eddie",
        "Alex Kidd",
        "Alex Shelley",
        "Taradino Cassatt",
        "Tails",
        "T. Hawk",
        "Superman",
        "Alta�r Ibn-La'Ahad",
        "Amigo",
        "Super Macho Man",
        "Super Joe",
        "Strider Hiryu",
        "Sting",
        "Hugo Andore",
        "Andy Bogard",
        "April O'Neil",
        "The Arbiter",
        "Axl",
        "Bald Bull",
        "Balrog",
        "Bam Margera",
        "Banjo",
        "Baraka",
        "Bart Simpson",
        "Batman",
        "Bear Hugger",
        "Splinter",
        "Spider-Man",
        "Sonya Blade",
        "Starman",
        "Sonjay Dutt",
        "Sonic the Hedgehog",
        "Sonia Belmont",
        "Solid Snake",
        "Soleiyu Belmont",
        "Sodom",
        "Smoke",
        "Slick",
        "Ugg",
        "Slash",
        "Slash",
        "Skullomania",
        "Simons",
        "Simon Belmont",
        "Shredder",
        "Sheeva",
        "Shark Boy",
        "Shaquille O'Neal",
        "Shao Kahn",
        "Shang Tsung",
        "Low Ki",
        "Sektor",
        "Scott Steiner",
        "Sarah Kerrigan",
        "Sarah Bryant",
        "Samus Aran",
        "Samoa Joe",
        "Sam Fisher",
        "Sagat",
        "Ryu",
        "Ryu Hayabusa",
        "Roman Bellic",
        "Rolento",
        "Rodney Recloose",
        "Rock Howard",
        "RoboCop",
        "Robert Roode",
        "Robert Garcia",
        "Robbit",
        "Horace Belger",
        "Bentley Bear",
        "Riddick",
        "Richter Belmont",
        "Rhino",
        "Reinhardt Schneider",
        "Rayne",
        "Ratchet",
        "Raphael",
        "Ramon Solano",
        "Rain",
        "Nathan \"Rad\" Spencer",
        "Raiden",
        "Quickclaw",
        "Q*Bert",
        "Psycho Fox",
        "Bif",
        "Bill Bull",
        "Bill Clinton",
        "Bionic Lester",
        "Blake Stone",
        "Poseur Pete",
        "Poison",
        "Playboy X",
        "Blinky",
        "Blue Mary",
        "Bob",
        "Pitfall Harry",
        "Pit",
        "Piston Hurricane",
        "Pinky",
        "Pikachu",
        "Petey Williams",
        "Pete Kowalski",
        "Paul Phoenix",
        "Pac-Man",
        "Otacon",
        "Optimus Prime",
        "Noob Saibot",
        "The Noid",
        "Niko Bellic",
        "Nick",
        "Nevin",
        "Soda Popinski",
        "Piston Hondo",
        "Stevedore",
        "Neo",
        "Nei",
        "Nathan Drake",
        "Nakoruru",
        "Mutoid Man",
        "Ms. Pac-Man",
        "Mr. Sandman",
        "Mr. Pants",
        "Motaro",
        "Heishiro Mitsurugi",
        "Mike Tyson",
        "Mike Haggar",
        "Michelangelo",
        "Michael Jackson",
        "Mattias Nilsson",
        "Master Chief",
        "Mario",
        "Maria Renard",
        "Marcus Fenix",
        "Marco Rossi",
        "Mai Shiranui",
        "Madfox Daimyojin",
        "MacCarver",
        "M. Bison",
        "Lorelei Ni",
        "Lo Wang",
        "Liu Kang",
        "Little Jacob",
        "Liquid Snake",
        "Link",
        "Leonardo",
        "Leon Belmont",
        "Lazlow Jones",
        "Lara Croft",
        "Lance Vance",
        "Kyo Kusanagi",
        "Kurtis Stryker",
        "Kurt Angle",
        "Kool-Aid Man",
        "Konoko",
        "Knuckles",
        "Kintaro",
        "King Slender",
        "King Hippo",
        "Kin Corn Karn",
        "Kim Kaphwan"
    );

    public $nb_votes = 0;

    public $avatar_urls = array(
        'small' => '',
        'medium' => '',
        'big' => '',
        'orginal' => ''
    );

    public $is_valid = false;


    function __construct($idUser = NULL){
        if(!is_null($idUser)){
            //INIT
            $this->id = $idUser;
            $this->init();
        }
    }

    public function init ($data = null) {
        if(is_null($data)){
            if(!$data = $this->user_exists())
            {
                return false;
            }
        }
        $this->id = $data['id_user'];
        $this->hashid = $data['hashid_user'];
        $this->ip = $data['ip_user'];
        $this->created = $data['created_user'];
        $this->last_seen = $data['last_seen_user'];
        $this->points = $data['points_user'];
        $this->is_registered = $data['is_registered_user'];
        $this->register_at = $data['register_at_user'];
        $this->hash = $data['hash_user'];
        $this->email = $data['email_user'];
        $this->name = $data['name_user'];

        $this->avatar_urls = array(
            'small' => "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $this->email ) ) ). "&s=32?d=identicon",
            'medium' => "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $this->email ) ) ). "&s=128?d=identicon",
            'big' => "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $this->email ) ) ). "&s=256?d=identicon",
            'original' => "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $this->email ) ) ). "&s=1024?d=identicon"
        );

        $this->is_valid = true;

        $this->nb_votes = $this->get_nb_votes();
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

        $this->hashid = self::generate_guestid();

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
                case 'VOTE_NONE':
                    $arrayPoints['total'] += self::$PTS_VOTE_NONE;
                    $arrayPoints['list']['VOTE_NONE'] = self::$PTS_VOTE_NONE;
                    break;
            }
        }
        $_pointsBefore = $this->points;
        $this->addPoints($arrayPoints);

        $arrayPoints['grand_total'] = $this->points;

        $Activity = new Activity();
        $Activity->configure(
            $this->id,
            'POINTS',
            'POINTS_GET',
            array(
                'reasons' => $arrayActions,
                'points_before' => $_pointsBefore,
                'points_after' => $this->points
            )
        );
        $Activity->save();

        return $arrayPoints;
    }

    public function addPoints ($arrayPoints) {
        $req = "UPDATE ".self::$table." SET `points_user`= `points_user` + :points WHERE id_user = ".$this->id;

        $query = DB::$db->prepare($req);
        $query->bindParam(':points', $arrayPoints['total']);
        $query->execute();

        $this->points += $arrayPoints['total'];
    }

    public function update_registered(){
        //username
        //hash (password)
        //is_registered
        //registered_at
        //email

        $req = "UPDATE ".self::$table." SET `name_user`=:name, `hash_user`=:hash, `email_user`=:email, `register_at_user`=:register_at, `is_registered_user`=:is_registered WHERE id_user = ".$this->id;

        $query = DB::$db->prepare($req);
        $query->bindParam(':name', $this->name);
        $query->bindParam(':hash', $this->hash);
        $query->bindParam(':email', $this->email);
        $query->bindParam(':register_at', $this->register_at);
        $query->bindParam(':is_registered', $this->is_registered);
        $query->execute();
    }


    public function register(){
        //all attributs are already given

        if($UserWithThisGuestID = self::check_if_guestid_exists($this->hashid)){
            //if user already exist : update that user with:
            //username
            //hash (password)
            //is_registered
            //registered_at
            //email
            if($UserWithThisGuestID->is_registered == 0){
                $this->update_registered();
                $this->status = 'connected';
            }
        }else{
            //if no user, create him and save him
            $this->save();
            $this->update_registered();
            $this->status = 'connected';
        }
    }

    public function convert_in_array(){
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'is_registered' => $this->is_registered,
            'register_at' => $this->register_at,
            'email' => $this->email,
            'points' => $this->points,
            'hashid' => $this->hashid,
            'created' => $this->created,
            'last_seen' => $this->last_seen,
            'nb_votes' => $this->nb_votes,
            'avatar_urls' => $this->avatar_urls

        ];

        return $result;
    }

    public function convert_in_soft_array(){
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'points' => $this->points,
            'nb_votes' => $this->nb_votes,
            'avatar_urls' => $this->avatar_urls
        ];

        return $result;
    }

    public function get_nb_votes(){
        $req = 'SELECT count(*) as result FROM ' . DB_TABLE_FIGHTS . ' WHERE id_user_fight = :id_user AND is_done_fight LIMIT 1';
        $query = DB::$db->prepare($req);
        $query->bindParam(':id_user', $this->id);
        $query->execute();

        $data = $query->fetch();

        return $data['result'];
    }

    public function get_last_undone_fight(){
        //$req = 'SELECT id_fight FROM ' . DB_TABLE_FIGHTS . ' WHERE is_done_fight = 0 AND id_user_fight = :id_user ORDER BY date_created_fight DESC LIMIT 1';
        //Not really the last
        $req = 'SELECT id_fight FROM ' . DB_TABLE_FIGHTS . ' WHERE is_done_fight = 0 AND id_user_fight = :id_user ORDER BY date_created_fight ASC LIMIT 1';
        $query = DB::$db->prepare($req);
        $query->bindParam(':id_user', $this->id);
        $query->execute();

        if($data = $query->fetch())
        {
            return new Fight($data['id_fight']);
        }
        return false;
    }

    public function get_fight(){
        if($Fight = $this->get_last_undone_fight()){
            return $Fight;
        }else{
            return Fight::generate_fight($this->id);
        }
    }

    // STATIC

    static function check_if_email_exists($email){
        $req = 'SELECT * FROM ' . self::$table . ' WHERE email_user = :email LIMIT 1';
        $query = DB::$db->prepare($req);
        $query->bindParam(':email', $email);
        $query->execute();

        return $query->fetch();
    }

    static function check_if_guestid_exists($guestid){
        $req = 'SELECT * FROM ' . self::$table . ' WHERE hashid_user = :guestid LIMIT 1';
        $query = DB::$db->prepare($req);
        $query->bindParam(':guestid', $guestid);
        $query->execute();
        if($data = $query->fetch()){
            $User = new User($data['id_user']);
            $User->status = 'guest';

            return $User;
        }else{
            return false;
        }
    }

    static function check_if_email_is_register($email){
        $req = 'SELECT * FROM ' . self::$table . ' WHERE email_user = :email AND is_registered_user = 1 LIMIT 1';
        $query = DB::$db->prepare($req);
        $query->bindParam(':email', $email);
        $query->execute();
        if($data = $query->fetch()){
            $User = new User($data['id_user']);

            return $User;
        }else{
            return false;
        }
    }

    static function generate_hashid ($id) {
        $HASHIDS = new Hashids\Hashids(self::$salt_hashid, 255 , 'abcdefghij1234567890');
        return $HASHIDS->encode($id);
    }

    static function generate_password ($password){
        return hash('sha256', $password.self::$salt_password);
    }

    static function check_password($password, $hash){
        return (User::generate_password($password) == $hash);
    }

    static function get_user_from_guestid_cookie(){
        if(isset($_COOKIE['guest_id']) AND !empty($_COOKIE['guest_id'])){
            return self::check_if_guestid_exists($_COOKIE['guest_id']);
        }else{
            $UserGuest = new User();
            $UserGuest->created = date("Y-m-d H:i:s");
            $UserGuest->last_seen = $UserGuest->created;
            $UserGuest->ip = $_SERVER['REMOTE_ADDR'];

            $UserGuest->save();
            $UserGuest->status = 'guest';
        }
        self::set_guestid_cookie($UserGuest->hashid);
        return $UserGuest;
    }

    static function set_guestid_cookie($guestid){
        setcookie('guest_id', $guestid, time()+3600*24*30);
    }


    static function delete_guestid_cookie(){
        unset($_COOKIE['guest_id&']);
        setcookie('guest_id', '', time() - 3600, '/');
    }

    static function generate_random_username(){
        $rand_keys = array_rand(self::$array_characters, 1);
        $character = self::$array_characters[$rand_keys];

        return $character . ' #' . rand(1, 1000);
    }

    static function generate_guestid(){
        return md5(uniqid(rand(), true)).md5(uniqid(rand(), true)).md5(uniqid(rand(), true));
    }

    static function get_current_user(){
        $User = new User();
        if ($userID = PersistentAuth::cookieLogin ()) {
            $User = new User($userID);
            $User->status = 'connected';
        } else
        {
            $User = User::get_user_from_guestid_cookie ();
            $User->status = 'guest';
        }
        return $User;
    }

    static function get_leaderboard($period = 7, $limit = 10){
        if(is_null($period)){
            return self::get_leaderboard_all_time($limit);
        }
        $arrayActivities = Activity::get_activites($period, null, 'POINTS');
        $leaderboard = array();

        $userPoints = array();
        foreach($arrayActivities as $Activity){
            if(isset($userPoints[$Activity->id_user])){
                $userPoints[$Activity->id_user] += $Activity->details_unserialized['points_diff'];
            }else{
                $userPoints[$Activity->id_user] = $Activity->details_unserialized['points_diff'];

            }
        }
        //sorting users
        arsort($userPoints);

        foreach ($userPoints as $id_user => $points) {
            $User = new User($id_user);
            if($User->is_registered){
                $leaderboard[] = array(
                    'user' => $User->convert_in_soft_array(),
                    'points_period' => $points
                );
                if(count($leaderboard) >= $limit){
                    break;
                }
            }
        }

        return $leaderboard;
    }


    static function get_leaderboard_all_time($limit = 10){

        $req = 'SELECT * FROM ' . self::$table . ' WHERE is_registered_user = 1 ORDER BY points_user DESC LIMIT '.$limit;
        $query = DB::$db->prepare($req);
        $query->bindParam(':email', $email);
        $query->execute();

        $leaderboard = array();
        while($data = $query->fetch()){
            $User = new User();
            $User->init($data);

            $leaderboard[] = array(
                'user' => $User->convert_in_soft_array(),
                'points_period' => $User->points
            );
        }

        return $leaderboard;
    }
}