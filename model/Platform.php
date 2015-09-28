<?php
    /**
     * Created by PhpStorm.
     * User: Personne
     * Date: 28/08/2015
     * Time: 20:53
     */

    class Platform {

        static $table = DB_TABLE_PLATFORMS;

        public $id;

        public $name;

        public $api_url;

        public $abbreviation;


        function __construct ($idPlatform = NULL)
        {
            if (!is_null ($idPlatform)) {
                /* INITIALISATION */
                $this->id = $idPlatform;
                $this->init ();
            }
        }

        public function platform_exists ()
        {
            $req = DB::$db->query ('SELECT * FROM ' . DB_TABLE_PLATFORMS . ' WHERE id_platform = "' . $this->id . '" LIMIT 1');
            return $req->fetch();
        }

        public function init ()
        {
            if ($data = $this->platform_exists ()) {

                $this->id = $data['id_platform'];
                $this->name = $data['name_platform'];
                $this->api_url = $data['api_url_platform'];
                $this->abbreviation = $data['abbreviation_platform'];

            }
        }

        public function convert_from_gb($PlatformGB){
            $this->api_url = $PlatformGB->api_detail_url;
            $this->name = $PlatformGB->name;
            $this->id = $PlatformGB->id;
            $this->abbreviation = $PlatformGB->abbreviation;
        }

        public function save(){
            $req = "INSERT IGNORE INTO ".self::$table."
                    (`id_platform`, `api_url_platform`, `name_platform`, `abbreviation_platform`)
                    VALUES (:id,:api_url,:name,:abbreviation)";

            $query = DB::$db->prepare($req);
            $query->bindParam(':id', $this->id);
            $query->bindParam(':name', $this->name);
            $query->bindParam(':api_url', $this->api_url);
            $query->bindParam(':abbreviation', $this->abbreviation);
            $query->execute();
        }

        public function convert_in_array(){
            $result = [
                'id' => $this->id,
                'name' => $this->name,
                'abbreviation' => $this->abbreviation
            ];

            return $result;
        }

    }