<?php
/**
 * Created by PhpStorm.
 * User: Personne
 * Date: 25/08/2015
 * Time: 23:09
 */
    require "./model/core.php";
    /*
    $gb_obj = new GiantBomb(GIANTBOMB_API_KEY);

    for($j = 4; $j < 10; $j++){
        $AllPop =$gb_obj->games(null, 100, (101*$j), null, array('number_of_user_reviews:desc'), array('name', 'id', 'image', 'original_release_date', 'deck', 'platforms','api_detail_url', 'images'))->results;

        for($i = 0; $i<count($AllPop); $i++){
            $Game = $AllPop[$i];
            $GameToSave = new Game();

            $GameToSave->convert_from_gb($Game);
            $GameToSave->save();
        }
    }
    die();
    //*/
    // CHECK GUEST
    if(isset($_COOKIE['guest_id']) AND !empty($_COOKIE['guest_id'])){
        $UserGuest = new User();
        $UserGuest->hashid = $_COOKIE['guest_id'];
        $UserGuest->id = $UserGuest->decodeHashid();
        $UserGuest->init();
        setcookie('guest_id', $UserGuest->hashid, time()+3600*24*30);

        //var_dump('user getted : '.$UserGuest->id.'/'.$UserGuest->hashid);
    }else{
        $UserGuest = new User();
        $UserGuest->created = date("Y-m-d H:i:s");
        $UserGuest->last_seen = $UserGuest->created;
        $UserGuest->ip = $_SERVER['REMOTE_ADDR'];

        $UserGuest->save();


        setcookie('guest_id', $UserGuest->hashid, time()+3600*24*30);

        //var_dump('user created : '.$UserGuest->id.'/'.$UserGuest->hashid);
    }
?>
<html>
    <head>
        <base href="<?= URL_BASE_HREF;?>">
        <title>GameMash</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel = "stylesheet" href = "assets/stylesheets/main.css" />
    </head>
    <body ng-app="appGameMash">
    <div id="main">
        <div class="container-nav">
            <ul>
                <li class="nav" ng-class="{active: $root.activetab == 'fight'}">
                    <a href = "./fight">Vote</a>
                </li>
                <li class="nav" ng-class="{active: $root.activetab == 'top'}">
                    <a href = "./top">Top 100</a>
                </li>
                <li class="nav coming-soon disabled" ng-class="{active: $root.activetab == 'leaderboard'}">
                    <a href = "./fight">Leaderboard</a>
                </li>
                <li class="nav coming-soon disabled" ng-class="{active: $root.activetab == 'stats'}">
                    <a href = "./fight">Cool statistics</a>
                </li>
            </ul>
        </div>
        <div ng-view></div>
    </div>
    </body>

    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.16/angular.min.js"></script>
    <script src="https://code.angularjs.org/1.3.16/angular-route.min.js"></script>
    <script src="./assets/js/ng-infinite-scroll.min.js"></script>
    <script src="./assets/js/main.js"></script>

</html>