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
        $id_user_fight = -1;
        if(isset($_COOKIE['guest_id']) AND !empty($_COOKIE['guest_id']))
        {
            $UserGuest = new User();
            $UserGuest->hashid = $_COOKIE['guest_id'];
            $UserGuest->id = $UserGuest->decodeHashid();
            $UserGuest->init();

            $id_user_fight = $UserGuest->id;
        }
        $Fight = new Fight();
        $Fight->create_fight($GameLeft,$GameRight, $id_user_fight);
        $Fight->save();

        $json['id'] = $Fight->id;
        $json['token'] = $Fight->token;
        echo json_encode($json);
    })->name("getFight");

    $app->post('/vote', function() use($app) {
        $allParamsPOST = $app->request->post();
        $id_fight = $allParamsPOST['id_fight'];
        //TODO: do something with that token
        $token_fight = $allParamsPOST['token_fight'];
        $Fight = new Fight($id_fight);

        if($Fight->token != $token_fight){
            return false;
        }

        $GameLeft = new Game($Fight->id_game_left);
        $GameRight = new Game($Fight->id_game_right);
        $side = $allParamsPOST['side'];

        $isLeft = $isRight = 0;
        if($side == 'left'){
            $isLeft = 1;
        }else if($side == 'right'){
            $isRight = 1;
        }else{
            $isLeft = 0.5;
            $isRight = 0.5;
        }
        $rating = new \Rating\Rating($GameLeft->current_elo, $GameRight->current_elo, $isLeft, $isRight);
        $newRating = $rating->getNewRatings();

        $GameLeft->current_elo = $newRating['a'];
        $GameLeft->updateELO();
        $GameRight->current_elo = $newRating['b'];
        $GameRight->updateELO();

        $Fight->set_vote($side);
        $Fight->saveVote();

        // POINTS
        if(isset($_COOKIE['guest_id']) AND !empty($_COOKIE['guest_id']))
        {
            $UserGuest = new User();
            $UserGuest->hashid = $_COOKIE['guest_id'];
            $UserGuest->id = $UserGuest->decodeHashid();
            $UserGuest->init();

            $arrayActions = array();
            $arrayActions[] = 'VOTE';

            $msNow = (int)microtime(true)*1000;
            $diffCreatedVoted = $msNow - dateTimeToMilliseconds(new DateTime($Fight->date_created));

            if($diffCreatedVoted <= User::$TIME_FAST_VOTE){
                $arrayActions[] = 'FAST_VOTE';
            }

            $arrayPoints = $UserGuest->getPoints($arrayActions);

            echo json_encode($arrayPoints);
        }

    });

    $app->get('/user/points', function() use($app) {
        $json = array();
        $json['user']['points'] = 0;
        if(isset($_COOKIE['guest_id']) AND !empty($_COOKIE['guest_id']))
        {
            $UserGuest = new User();
            $UserGuest->hashid = $_COOKIE['guest_id'];
            $UserGuest->id = $UserGuest->decodeHashid();
            $UserGuest->init();
            $json['user'] = $UserGuest;
        }

        echo json_encode($json);
    });

    $app->get('/top', function() use($app) {
        $paramsGET = $app->request->get();
        $page = $paramsGET['page'];
        $json = array();
        $GamesTop = Game::get_top_games($page);
        $i = (($page-1)*10)+1;
        if($page >= 11){
            return json_encode($json);
        }
        foreach($GamesTop as $Game){
            $gameEntry = $Game->convert_in_array();
            $gameEntry['rank'] = $i;

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


    $app->run();
