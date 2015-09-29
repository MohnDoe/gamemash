<?php
/**
 * Created by PhpStorm.
 * User: Personne
 * Date: 28/08/2015
 * Time: 20:53
 */

class Game {

    static $table = DB_TABLE_GAMES;

    public $id;

    public $name;

    public $api_url;

    public $deck;

    public $cover_serialized = "";

    public $cover_unserialized = [];

    public $images_serialized = "";

    public $images_unserialized = [];

    public $platforms_array = array();

    public $release_date;

    // ELO RELATED STUFF
    public $aAllFightsDone = [];
    public $current_elo = 1400;
    public $nb_matchs = 0;
    public $nb_matchs_won = 0;
    public $nb_matchs_lost = 0;
    public $nb_matchs_draw = 0;
    public $elo_performance;


    function __construct ($idGame = NULL)
    {
        if (!is_null ($idGame)) {
            /* INITIALISATION */
            $this->id = $idGame;
            $this->init ();
        }
    }

    public function game_exists ()
    {
        $req = DB::$db->query ('SELECT * FROM ' . DB_TABLE_GAMES . ' WHERE id_game = "' . $this->id . '" LIMIT 1');
        return $req->fetch();
    }

    public function init ()
    {
        if ($data = $this->game_exists ()) {

            $this->id = $data['id_game'];
            $this->name = $data['name_game'];
            $this->api_url = $data['api_url_game'];
            $this->deck = $data['deck_game'];

            $this->cover_serialized = $data['cover_game'];
            $this->cover_unserialized = unserialize($this->cover_serialized);

            $this->images_serialized = $data['images_game'];
            $this->images_unserialized = unserialize($this->images_serialized);

            $this->release_date = $data['release_date_game'];
            $this->current_elo = $data['current_elo_game'];

            $this->platforms_array = $this->getPlatforms();

            $this->initEloStats();

        }
    }

    static function get_top_games ($page = 1) {
        $perPage = 10;
        $req = DB::$db->query ('SELECT id_game FROM ' . DB_TABLE_GAMES . ' WHERE 1 ORDER BY current_elo_game DESC LIMIT '.(($page-1)*$perPage).','.$perPage);
        $results = [];

        while($data = $req->fetch()){
            $results[] = new Game($data['id_game']);
        }

        return $results;
    }

    public function convert_from_gb($GameGB){
        $this->api_url = $GameGB->api_detail_url;
        $this->deck = $GameGB->deck;
        $this->id = $GameGB->id;

        //images
        if(isset($GameGB->image)){
            $this->cover_unserialized = [
                'icon_url' => $GameGB->image->icon_url,
                'medium_url' => $GameGB->image->medium_url,
                'screen_url' => $GameGB->image->screen_url,
                'small_url' => $GameGB->image->small_url,
                'super_url' => $GameGB->image->super_url,
                'thumb_url' => $GameGB->image->thumb_url,
                'tiny_url' => $GameGB->image->tiny_url
            ];
            $this->cover_serialized = serialize($this->cover_unserialized);
        }
        //*
        if(isset($GameGB->platforms)){
            for($i = 1; $i < count($GameGB->platforms); $i++){
                $platform = $GameGB->platforms[$i];
                $Platform = new Platform();
                $Platform->convert_from_gb($platform);
                $Platform->save();
                $this->platforms_array[] = $Platform;
            }
        }
        //*/

        $gb_obj = new GiantBomb(GIANTBOMB_API_KEY);
        $GameFromGB = $gb_obj->game($this->id, ['images']);

        if(isset($GameFromGB->results->images)){
            for($i = 1; $i < count($GameFromGB->results->images) && $i < 5; $i++){
                $image = $GameFromGB->results->images[$i];
                $this->images_unserialized[] = [
                    'icon_url' => $image->icon_url,
                    'medium_url' => $image->medium_url,
                    'screen_url' => $image->screen_url,
                    'small_url' => $image->small_url,
                    'super_url' => $image->super_url,
                    'thumb_url' => $image->thumb_url,
                    'tiny_url' => $image->tiny_url
                ];
            }
        }
        $this->images_serialized = serialize($this->images_unserialized);


        $this->name = $GameGB->name;

        $this->release_date = $GameGB->original_release_date;
    }

    public function save(){
        $req = "INSERT IGNORE INTO ".self::$table."
                    (`id_game`, `name_game`, `api_url_game`, `deck_game`, `cover_game`, `images_game`, `release_date_game`, `current_elo_game`)
                    VALUES (:id,:name,:api_url,:deck,:cover,:images,:release_date,:current_elo)";

        $query = DB::$db->prepare($req);
        $query->bindParam(':id', $this->id);
        $query->bindParam(':name', $this->name);
        $query->bindParam(':api_url', $this->api_url);
        $query->bindParam(':deck', $this->deck);
        $query->bindParam(':cover', $this->cover_serialized);
        $query->bindParam(':images', $this->images_serialized);
        $query->bindParam(':release_date', $this->release_date);
        $query->bindParam(':current_elo', $this->current_elo);
        $query->execute();

        $this->savePlatforms();
    }

    public function updateELO(){
        $req = "UPDATE ".self::$table." SET `current_elo_game`=:current_elo WHERE id_game = ".$this->id;

        $query = DB::$db->prepare($req);
        $query->bindParam(':current_elo', $this->current_elo);
        $query->execute();
    }

    static function get_random_games($many = 2){
        $req = DB::$db->query ("SELECT id_game FROM ".DB_TABLE_GAMES." ORDER BY RAND() LIMIT ".$many);

        $d = [];

        while($data = $req->fetch()){
            $d[] = new Game($data['id_game']);
        }

        return $d;
    }

    public function savePlatforms () {
        for($i = 1; $i < count($this->platforms_array); $i++){
            $Platform = $this->platforms_array[$i];

            $req = "INSERT INTO ".DB_TABLE_IS_IN_PLATFORM."
                    (`id_game`, `id_platform`)
                    VALUES (:id_game, :id_platform)";

            $query = DB::$db->prepare($req);
            $query->bindParam(':id_game', $this->id);
            $query->bindParam(':id_platform', $Platform->id);
            $query->execute();
        }
    }

    public function getPlatforms(){
        $req = DB::$db->query ('SELECT `id_platform` FROM `'.DB_TABLE_IS_IN_PLATFORM.'` WHERE id_game = '.$this->id.' GROUP BY  `id_platform`');
        $result = [];
        while($data = $req->fetch()){
            $result[] = new Platform($data['id_platform']);
        }
        return $result;
    }

    public function convert_in_array(){
        $date = new DateTime($this->release_date);
        $yearGame = $date->format('Y');

        $aPlatforms = array();
        foreach($this->platforms_array as $Platform){
            $aPlatforms[] = $Platform->convert_in_array();
        }

        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'url_image' => $this->images_unserialized[0]['super_url'],
            'url_cover' => $this->cover_unserialized['small_url'],
            'year' => $yearGame,
            'release_date' => $date->format('M j\, Y'),
            //ELO STUF
            'current_elo' => $this->current_elo,
            'nb_matchs' => $this->nb_matchs,
            'nb_matchs_won' => $this->nb_matchs_won,
            'nb_matchs_lost' => $this->nb_matchs_lost,
            'nb_matchs_draw' => $this->nb_matchs_draw,
            'elo_performance' => $this->elo_performance,
            'platforms' => $aPlatforms,
            'developers' => 'Unkown Developer'
        ];

        return $result;
    }

    public function getAllFightsDone(){
        $req = DB::$db->query ('SELECT id_fight FROM ' . DB_TABLE_FIGHTS . ' WHERE (id_game_right = "' . $this->id . '" OR id_game_left = "' . $this->id . '") AND is_done_fight = 1 ORDER BY date_voted_fight ASC');
        $result = [];
        while($data = $req->fetch()){
            $result[] = new Fight($data['id_fight']);
        }
        $this->aAllFightsDone = $result;
        return $result;
    }

    public function initEloStats () {
        $this->getAllFightsDone();
        //NUMBER OF FIGHTS
        $this->nb_matchs = count($this->aAllFightsDone);

        //foreach fight check when the game won
        // = was left + left won || was right + right won
        $this->nb_matchs_won = 0;
        $this->nb_matchs_lost = 0;
        foreach($this->aAllFightsDone as $Fight){
            if($Fight->id_game_winner == $this->id){
                //NUMBER OF GAME WON
                $this->nb_matchs_won++;
            }else if($Fight->id_game_looser == $this->id){
                //NUMBER OF GAME LOST
                $this->nb_matchs_lost++;
            }
        }
        //NUMBER OF GAME DRAW
        // total - won - lost
        $this->nb_matchs_draw = $this->nb_matchs - $this->nb_matchs_lost - $this->nb_matchs_won;
        //ELO PERFORMANCE
        $this->elo_performance = ($this->nb_matchs_won + ($this->nb_matchs_draw*0.5)) / $this->nb_matchs * 100;
    }

}