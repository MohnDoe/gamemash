<?php
    /**
     * Created by PhpStorm.
     * User: Personne
     */

    require '../model/core.php';
    require '../vendor/autoload.php';


    // INIT SLIM
    \Slim\Slim::registerAutoloader();

    $app = new \Slim\Slim([
                              'debug' => true,
                          ]);



    $app->get('/fight', function() use($app) {

        $json = array();
        $GamesFight = Game::get_random_games(2);

        $GameLeft = $GamesFight[0];
        $json['gameLeft'] = $GameLeft->convert_in_array();

        $GameRight = $GamesFight[1];
        $json['gameRight'] = $GameRight->convert_in_array();

        //saving fight
        $User = User::get_current_user();
        $Fight = new Fight();
        $Fight->create_fight($GameLeft,$GameRight, $User->id);
        $Fight->save();

        $json['id'] = $Fight->id;
        $json['token'] = $Fight->token;
        echo json_encode($json);
    })->name("getFight");

    $app->post('/vote', function() use($app) {
        $allParamsPOST = $app->request->post ();
        $id_fight = $allParamsPOST['id_fight'];
        //TODO: do something with that token
        $token_fight = $allParamsPOST['token_fight'];
        $Fight = new Fight($id_fight);

        if ($Fight->token != $token_fight) {
            return false;
        }

        $GameLeft = new Game($Fight->id_game_left);
        $GameRight = new Game($Fight->id_game_right);
        $side = $allParamsPOST['side'];

        $isLeft = $isRight = 0;
        if ($side == 'left') {
            $isLeft = 1;
        } else if ($side == 'right') {
            $isRight = 1;
        } else {
            $isLeft = 0.5;
            $isRight = 0.5;
        }
        $rating = new \Rating\Rating($GameLeft->current_elo, $GameRight->current_elo, $isLeft, $isRight);
        $newRating = $rating->getNewRatings ();

        $GameLeft->current_elo = $newRating['a'];
        $GameLeft->updateELO ();
        $GameRight->current_elo = $newRating['b'];
        $GameRight->updateELO ();

        $Fight->set_vote ($side);
        $Fight->saveVote ();

        $User = User::get_current_user();
        // POINTS
        $arrayActions = [];
        $arrayActions[] = 'VOTE';

        $msNow = (int)microtime (true) * 1000;
        $diffCreatedVoted = $msNow - dateTimeToMilliseconds (new DateTime($Fight->date_created));

        if ($diffCreatedVoted <= User::$TIME_FAST_VOTE) {
            $arrayActions[] = 'FAST_VOTE';
        }

        $arrayPoints = $User->getPoints($arrayActions);

        echo json_encode ($arrayPoints);
    });

    $app->get('/user/points', function() use($app) {
        $json = array();
        $User = User::get_current_user();
        $json['user'] = $User->convert_in_array();

        echo json_encode($json);
    });

    $app->get('/user', function() use($app) {
        $json = array();
        $User = User::get_current_user();
        $json['user'] = $User->convert_in_array();
        $json['status'] = $User->status;

        echo json_encode($json);
    });

    $app->get('/top', function() use($app) {
        $paramsGET = $app->request->get();
        $page = $paramsGET['page'];
        $json = array();
        $GamesTop = Game::get_top_games($page);
        //$i = (($page-1)*10)+1;
        $i = (($page-1)*10)+1;
        if($page >= 11){
            return json_encode($json);
        }
        foreach($GamesTop as $Game){
            $gameEntry = $Game->convert_in_array();
            $gameEntry['rank'] = $i;
            $gameEntry['platforms_string'] = "";
            for($j = 0; $j < count($Game->platforms_array); $j++){
                $gameEntry['platforms_string'] .= $Game->platforms_array[$j]->abbreviation;
                if($j != count($Game->platforms_array) - 1)
                {
                    $gameEntry['platforms_string']  .= '_';
                }
            }
            if($gameEntry['platforms_string'] == ""){
                $gameEntry['platforms_string'] = 'Unknown';
            }

            $json['games'][] = $gameEntry;
            $i++;
        }
        echo json_encode($json);
    })->name("getTopt");

    $app->get('/stats', function() use($app) {
        $votes_stats = Fight::get_statistics();
        $json = $votes_stats;
        //$json['nb_votes_formatted'] = number_format($votes_stats['nb_votes']);
        //$json['nb_votes_KMBT_format'] = thousandsCurrencyFormat($votes_stats['nb_votes']);
        echo json_encode($json);
    })->name("getFight");

    $app->get('/levels', function() use($app) {
        $Level = new Level();
        $Level::init();
        $json['levels'] = $Level::$aLevels;
        echo json_encode($json);
    })->name("getFight");

    $app->post('/user/register', function() use($app){
        $allParamsPOST = $app->request->post();

        $json_r = array();

        $json_r['status'] = '';
        $password = $allParamsPOST['password'];
        $email = $allParamsPOST['email'];

        $UserGuest = User::get_user_from_guestid_cookie();
        //check if a user existe with this email
        if($UserWithThisEmail = User::check_if_email_is_register($email)){
            if(User::check_password($password, $UserWithThisEmail->hash)){
                //pass are good
                $json_r['status'] = 'connected';

                User::delete_guestid_cookie();
                PersistentAuth::login($UserWithThisEmail->id);

                $json_r['user'] = $UserWithThisEmail->convert_in_array();
            }else{
                //not good
                $json_r['status'] = 'not connected';
                $json_r['error_message'] = 'An account already exists with this email, but the password doesn\'t match';
            }
        }else{
            //no user with this email, so register this user
            $UserGuest->is_registered = 1;
            $UserGuest->register_at = date('Y-m-d H:i:s');
            $UserGuest->hash = User::generate_password($password);
            $UserGuest->email = $email;
            $UserGuest->name = User::generate_random_username();

            $UserGuest->register();

            $json_r['status'] = 'registered';



            User::delete_guestid_cookie();
            PersistentAuth::login($UserGuest->id);
            $json_r['user'] = $UserGuest->convert_in_array();
        }

        echo json_encode($json_r);

    });


    $app->run();
