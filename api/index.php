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


    //Grouping API
    $app->group('/', function() use ($app){
        $json_response = array(
            'status' => '',
            'error' => '',
            //'response' => array()
        );
        //check if user is valid, if not, do nothing
        if(!$CurrentUser = User::get_current_user()){
            // no valid user, neither guest or logged

            $json_response['status'] = 'error';
            $json_response['error'] = 'invalid user';

            echo json_encode($json_response);
            exit();
        }

        $app->get('fight', function() use($app, $CurrentUser, $json_response) {
            $Fight = $CurrentUser->get_fight();

            //JSON RESPONSE
            $json_response['response']['fight'] = $Fight->convert_in_array();
            $json_response['status'] = 'OK';

            echo json_encode($json_response);
            exit();
        })->name("getFight");

        $app->post('vote', function() use($app, $CurrentUser, $json_response) {
            $allParamsPOST = $app->request->post ();
            $id_fight = $allParamsPOST['id_fight'];
            //TODO: do something with that token
            $token_fight = $allParamsPOST['token_fight'];
            $Fight = new Fight($id_fight);

            if($Fight->is_done){
                $json_response['error_message'] = 'Fight already done';
                $json_response['status'] = 'NOTOK';
                echo json_encode($json_response);
                exit();
            }
            if ($Fight->token != $token_fight) {
                return false;
            }

            $GameLeft = new Game($Fight->id_game_left);
            $GameRight = new Game($Fight->id_game_right);
            $side = $allParamsPOST['side'];

            $isLeft = $isRight = 0;

            // POINTS
            $arrayActions = [];
            if ($side == 'left') {
                $isLeft = 1;
                $arrayActions[] = 'VOTE';
            } else if ($side == 'right') {
                $isRight = 1;
                $arrayActions[] = 'VOTE';
            } else {
                $isLeft = 0.5;
                $isRight = 0.5;
                $arrayActions[] = 'VOTE_NONE';
            }
            //ELO RATING
            $rating = new \Rating\Rating($GameLeft->current_elo, $GameRight->current_elo, $isLeft, $isRight);
            $newRating = $rating->getNewRatings ();
            //saving new elo
            $GameLeft->current_elo = $newRating['a'];
            $GameLeft->updateELO ();
            $GameRight->current_elo = $newRating['b'];
            $GameRight->updateELO ();
            //saving the vote
            $Fight->set_vote ($side);
            $Fight->saveVote ();

            //TODO : put that in the Fight class
            $msNow = (int)microtime (true) * 1000;
            $diffCreatedVoted = $msNow - dateTimeToMilliseconds (new DateTime($Fight->date_created));

            if ($diffCreatedVoted <= User::$TIME_FAST_VOTE) {
                $arrayActions[] = 'FAST_VOTE';
            }
            $arrayPoints = $CurrentUser->getPoints($arrayActions);

            $json_response['response']['points'] = $arrayPoints;
            $json_response['response']['user'] = $CurrentUser->convert_in_array();

            $Fight = $CurrentUser->get_fight();

            $json_response['response']['fight'] = $Fight->convert_in_array();
            $json_response['status'] = 'OK';
            echo json_encode($json_response);
            exit();
        });

        $app->get('user', function() use($app, $CurrentUser, $json_response) {
            $json_response['response']['user'] = $CurrentUser->convert_in_array();
            $json_response['response']['status'] = $CurrentUser->status;
            $json_response['status'] = 'OK';
            echo json_encode($json_response);
            exit();
        });


        $app->get('leaderboard/:period', function($period) use($app, $json_response) {
            switch($period)
            {
                case 'all':
                    $periodNumber = null;
                    break;
                case 'month':
                    $periodNumber = 30;
                    break;
                case 'week':
                    $periodNumber = 7;
                    break;
                case 'today':
                    $periodNumber = 1;
                    break;
                default:
                    $periodNumber = null;
                    break;
            }
            $json_response['response']['leaderboard'] = User::get_leaderboard($periodNumber, 10);
            $json_response['status'] = 'OK';
            echo json_encode($json_response);
            exit();
        });


        $app->get('performance_of_the_:period', function($period) use($app, $json_response) {
            switch($period)
            {
                case 'all':
                    $periodNumber = null;
                    break;
                case 'month':
                    $periodNumber = 30;
                    break;
                case 'week':
                    $periodNumber = 7;
                    break;
                case 'today':
                    $periodNumber = 1;
                    break;
                default:
                    $periodNumber = null;
                    break;
            }
            $arrayGames = Game::get_best_elo_performances($periodNumber);

            reset( $arrayGames );
            $first_key = key( $arrayGames );
            $Game = new Game($first_key);

            $json_response['response']['game'] = $Game->convert_in_array();
            $json_response['status'] = 'OK';
            echo json_encode($json_response);
            exit();
        });


        $app->get('votes', function() use($app, $json_response) {
            $arrayVotes = Fight::get_fights_done(null, 10);

            $json_response['response']['votes'] = $arrayVotes;
            $json_response['status'] = 'OK';
            echo json_encode($json_response);
            exit();
        });

        $app->get('game_of_the_:period', function($period) use($app, $json_response) {
            switch($period)
            {
                case 'all':
                    $periodNumber = null;
                    break;
                case 'month':
                    $periodNumber = 30;
                    break;
                case 'week':
                    $periodNumber = 7;
                    break;
                case 'today':
                    $periodNumber = 1;
                    break;
                default:
                    $periodNumber = null;
                    break;
            }
            $arrayGames = Game::get_best_games($periodNumber);

            reset( $arrayGames );
            $first_key = key( $arrayGames );
            $Game = new Game($first_key);

            $json_response['response']['game'] = $Game->convert_in_array();
            $json_response['status'] = 'OK';
            echo json_encode($json_response);
            exit();
        });

        $app->get('top', function() use($app, $CurrentUser, $json_response) {
            $paramsGET = $app->request->get();
            $page = $paramsGET['page'];
            if($CurrentUser->status != 'connected'){
                $json_response['status'] = 'NOTOK';
                $json_response['error'] = 'Vous devez etre connecte pour pouvoir acceder aux classement des jeux.';
                echo json_encode($json_response);
                exit();
            }
            $GamesTop = Game::get_top_games($page);
            //$i = (($page-1)*10)+1;
            $i = (($page-1)*10)+1;
            if($page >= 11){
                $json_response['status'] = 'NOTOK';
                $json_response['error'] = 'Vous allez bien trop vite.';
                echo json_encode($json_response);
                exit();
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
                    $gameEntry['platforms_string'] = 'Inconnues';
                }

                $json_response['response']['games'][] = $gameEntry;
                $i++;
            }
            $json_response['status'] = 'OK';
            echo json_encode($json_response);
            exit();
        })->name("getTopt");

        $app->get('stats', function() use($app) {
            $votes_stats = Fight::get_statistics();
            $json_response['response'] = $votes_stats;
            //$json['nb_votes_formatted'] = number_format($votes_stats['nb_votes']);
            //$json['nb_votes_KMBT_format'] = thousandsCurrencyFormat($votes_stats['nb_votes']);
            $json_response['status'] = 'OK';
            echo json_encode($json_response);
            exit();
        })->name("getFight");

        $app->get('levels', function() use($app) {
            $Level = new Level();
            $Level::init();
            $json_response['response']['levels'] = $Level::$aLevels;
            $json_response['status'] = 'OK';
            echo json_encode($json_response);
            exit();
        })->name("getFight");

        $app->post('user/login', function() use($app, $json_response){
            $allParamsPOST = $app->request->post();
            $password = $allParamsPOST['password'];
            $email = $allParamsPOST['email'];

            //check if a user existe with this email
            if($UserWithThisEmail = User::check_if_email_is_register($email)){
                if(User::check_password($password, $UserWithThisEmail->hash)){
                    //pass are good

                    User::delete_guestid_cookie();
                    PersistentAuth::login($UserWithThisEmail->id);

                    $json_response['response']['status'] = 'connected';
                    $json_response['response']['user'] = $UserWithThisEmail->convert_in_array();

                    $json_response['status'] = 'OK';
                    echo json_encode($json_response);
                    exit();
                }else{
                    //not good
                    $json_response['response']['status'] = 'not connected';
                    $json_response['response']['error_message'] = utf8_encode('L\'adresse e-mail et le mot de passe ne correspondent pas.');
                    $json_response['status'] = 'OK';
                    echo json_encode($json_response);
                    exit();
                }
            }else{

                //not good
                $json_response['response']['status'] = 'not connected';
                $json_response['response']['error_message'] = utf8_encode('Aucun compte n\'est associ� � cette adresse e-mail');
                $json_response['status'] = 'OK';
                echo json_encode($json_response);
                exit();
            }

        });

        $app->post('user/register', function() use($app, $json_response, $CurrentUser){
            $app->response->headers->set('Content-Type', 'application/json');
            $allParamsPOST = $app->request->post();
            $password = $allParamsPOST['password'];
            $email = $allParamsPOST['email'];
            $name = $allParamsPOST['name'];
            //check if a user existe with this email
            if(User::check_if_email_is_register($email)){
                //not good
                $json_response['response'] = array(
                    'status' => 'not connected',
                    'error_message' => utf8_encode('Un compte est d�j� associ� � cette adresse email.')
                );
                $json_response['status'] = 'OK';
                echo json_encode($json_response);
                exit();
            }else{
                //no user with this email, so register this user
                $CurrentUser->is_registered = 1;
                $CurrentUser->register_at = date('Y-m-d H:i:s');
                $CurrentUser->hash = User::generate_password($password);
                $CurrentUser->email = $email;
                $CurrentUser->name = $name;

                $CurrentUser->register();




                User::delete_guestid_cookie();
                PersistentAuth::login($CurrentUser->id);

                $json_response['response']['status'] = 'connected';
                $json_response['response']['user'] = $CurrentUser->convert_in_array();
                $json_response['status'] = 'OK';
                echo json_encode($json_response);
                exit();
            }

        });
    });



    $app->run();
