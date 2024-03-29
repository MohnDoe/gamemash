<?php

    /**
     * Created by PhpStorm.
     * User: Personne
     * Date: 18/10/2015
     * Time: 23:23
     */
    class Activity
    {
        static $table = DB_TABLE_ACTIVITES;

        public $id;

        public $id_user;
        public $User;

        public $date;

        public $details_serialized;
        public $details_unserialized = array();

        public $category;

        public $action;


        function __construct($idActivity = NULL){
            if(!is_null($idActivity)){
                // INIT
                $this->id = $idActivity;
                $this->init();
            }
        }

        public function init ($data = null) {
            if(is_null($data)){
                if(!$data = $this->activity_exists())
                {
                    return false;
                }
            }

            $this->id = $data['id_activity'];
            $this->id_user = $data['id_user_activity'];
            $this->date = $data['date_activity'];

            $this->details_serialized = $data['details_activity'];
            $this->details_unserialized = unserialize($this->details_serialized);

            $this->category = $data['category_activity'];
            $this->action = $data['action_activity'];
        }

        public function activity_exists () {
            $req = DB::$db->query ('SELECT * FROM ' . self::$table . ' WHERE id_activiy = "' . $this->id . '" LIMIT 1');
            return $req->fetch();
        }

        public function create($arrayDetails, $category, $action, $date, $id_user){
            $this->details_unserialized = $arrayDetails;
            $this->details_serialized = serialize($this->details_unserialized);

            $this->category = $category;
            $this->action = $action;
            $this->date = $date;
            $this->id_user = $id_user;
        }

        public function save(){
            $req = "INSERT IGNORE INTO ".self::$table."
                    (`id_user_activity`, `date_activity`, `category_activity`, `action_activity`, `details_activity`)
                    VALUES (:id_user, :date, :category, :action, :details)";

            $query = DB::$db->prepare($req);
            $query->bindParam(':id_user', $this->id_user);
            $query->bindParam(':date', $this->date);
            $query->bindParam(':category', $this->category);
            $query->bindParam(':action', $this->action);
            $query->bindParam(':details', $this->details_serialized);
            $query->execute();

            $this->id = DB::$db->lastInsertId();

            return $this->id;
        }

        public function configure($id_user, $category, $action, $args = array()){

            $arrayDetails = array();
            $date = date("Y-m-d H:i:s");

            switch ($category)
            {
                case 'POINTS':
                    //CATEGORY POINTS
                    //can be get or lost

                    switch ($action)
                    {
                        case 'POINTS_GET':

                            $arrayDetails = array(
                                'reasons' => $args['reasons'],
                                'points_before' => $args['points_before'],
                                'points_after' => $args['points_after'],
                                'points_diff' => ($args['points_after'] - $args['points_before'])
                            );

                            break;
                    }
                    break;
            }


            $this->create($arrayDetails, $category, $action, $date, $id_user);
        }

        static function get_activites($period = null, $limit = null, $category = null, $action = null, $id_user = null)
        {
            $req = 'SELECT * FROM ' . self::$table;
            $req .= ' WHERE';
            if(!is_null($period)){
                $req .= ' date_activity > DATE_SUB(NOW(), INTERVAL '.$period.' DAY) AND';
            }
            if(!is_null($category)){
                $req .= ' category_activity = :category';
            }
            if(!is_null($action)){
                $req .= ' AND action_activity = :action';
            }
            if(!is_null($id_user)){
                $req .= ' AND id_user_activity = :id_user';
            }
            $req .= ' ORDER BY date_activity DESC';
            if(!is_null($limit)){
                $req .= ' LIMIT '.$limit;
            }

            $query = DB::$db->prepare($req);

            if(!is_null($category)){
                $query->bindParam(':category', $category);
            }
            if(!is_null($action)){
                $query->bindParam(':action', $action);
            }
            if(!is_null($id_user)){
                $query->bindParam(':id_user', $id_user);
            }
            $query->execute();

            $results = array();

            while($data = $query->fetch()){
                $Activity = new Activity();
                $Activity->init($data);
                $results[] = $Activity;
            }

            return $results;
        }

    }