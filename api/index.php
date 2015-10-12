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
            $GamesFight = Game::get_random_games(2);
            $GameLeft = $GamesFight[0];
            $GameRight = $GamesFight[1];

            //saving fight
            $Fight = new Fight();
            $Fight->create_fight($GameLeft,$GameRight, $CurrentUser->id);
            $Fight->save();

            //JSON RESPONSE
            $json_response['response'] = array(
                'id' => $Fight->id,
                'token' => $Fight->token,
                'gameRight' => $GameRight->convert_in_array(),
                'gameLeft' => $GameLeft->convert_in_array()
            );
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
            // POINTS
            $arrayActions = [];
            $arrayActions[] = 'VOTE';

            //TODO : put that in the Fight class
            $msNow = (int)microtime (true) * 1000;
            $diffCreatedVoted = $msNow - dateTimeToMilliseconds (new DateTime($Fight->date_created));

            if ($diffCreatedVoted <= User::$TIME_FAST_VOTE) {
                $arrayActions[] = 'FAST_VOTE';
            }
            $arrayPoints = $CurrentUser->getPoints($arrayActions);

            $json_response['response'] = $arrayPoints;
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

        $app->get('top', function() use($app, $CurrentUser, $json_response) {
            $paramsGET = $app->request->get();
            $page = $paramsGET['page'];
            $GamesTop = Game::get_top_games($page);
            //$i = (($page-1)*10)+1;
            $i = (($page-1)*10)+1;
            if($page >= 11){
                $json_response['status'] = 'NOTOK';
                $json_response['error'] = 'You\'re going too far my friend';
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
                    $gameEntry['platforms_string'] = 'Unknown';
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

        $app->post('user/register', function() use($app, $json_response){
            $allParamsPOST = $app->request->post();
            $password = $allParamsPOST['password'];
            $email = $allParamsPOST['email'];

            $UserGuest = User::get_user_from_guestid_cookie();
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
                    $json_response['response']['error_message'] = 'An account already exists with this email, but the password doesn\'t match';
                    $json_response['status'] = 'OK';
                    echo json_encode($json_response);
                    exit();
                }
            }else{
                //no user with this email, so register this user
                $UserGuest->is_registered = 1;
                $UserGuest->register_at = date('Y-m-d H:i:s');
                $UserGuest->hash = User::generate_password($password);
                $UserGuest->email = $email;
                $UserGuest->name = User::generate_random_username();

                $UserGuest->register();




                User::delete_guestid_cookie();
                PersistentAuth::login($UserGuest->id);

                $json_response['response']['status'] = 'registered';
                $json_response['response']['user'] = $UserGuest->convert_in_array();
                $json_response['status'] = 'OK';
                echo json_encode($json_response);
                exit();
            }

        });
    });



    $app->run();
