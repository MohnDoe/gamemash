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
    //$UserGuest = User::get_user_from_guestid_cookie();

    //TEST LEVEL
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
        <div class="container-user" ng-controller="userController">
            <div class="container-logged-user">

            </div>
        </div>
        <div ng-view></div>
    </div>
    <footer id="footer">
        <div id="container-user-level-progress" ng-controller="levelUserController">
            <div class="user-level-progress">
                <span class="current-level-name">{{levelsUser.previousAndPastLevels[0].name}}</span>
                <div class="container-progress-stats">
                    <span class="current-points">+{{user.points | number}} pts</span>
                    <span class="current-percentage">{{levelsUser.percentageGlobalCompletion | setDecimal:2}}%</span>
                </div>
                <!--
                <div class="container-informations-next-level-progress">
                    <span class="next-level-name">{{levelsUser.previousAndPastLevels[1].name}}</span>
                    <span class="next-points">+{{levelsUser.previousAndPastLevels[1].needed}} pts</span>
                </div>
                -->
                <div class="progress-bar">
                    <div class="fill-progress-bar" style="width: {{levelsUser.percentageGlobalCompletion}}%"></div>
                </div>
            </div>
        </div>
    </footer>
    </body>
    <!-- third -->
    <script src="./assets/js/lib/jquery.min.js"></script>
    <script src="./assets/js/lib/angular.min.js"></script>
    <script src="./assets/js/lib/angular-route.min.js"></script>
    <script src="./assets/js/ng-infinite-scroll.min.js"></script>
    <!-- app core -->
    <script src="./assets/js/main.js"></script>
    <!-- controllers -->
    <script src="./assets/js/controllers/fightController.js"></script>
    <script src="./assets/js/controllers/topController.js"></script>
    <script src="./assets/js/controllers/userController.js"></script>
    <script src="./assets/js/controllers/userLevelController.js"></script>
    <script src="./assets/js/controllers/loginController.js"></script>
    <!-- services -->
    <script src="./assets/js/services/gamemashStatisticsService.js"></script>
    <script src="./assets/js/services/topRankService.js"></script>
    <script src="./assets/js/services/userService.js"></script>

</html>