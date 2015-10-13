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
        <!--    SEGMENT -->
        <script type="text/javascript">
            !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","page","once","off","on"];analytics.factory=function(t){return function(){var e=Array.prototype.slice.call(arguments);e.unshift(t);analytics.push(e);return analytics}};for(var t=0;t<analytics.methods.length;t++){var e=analytics.methods[t];analytics[e]=analytics.factory(e)}analytics.load=function(t){var e=document.createElement("script");e.type="text/javascript";e.async=!0;e.src=("https:"===document.location.protocol?"https://":"http://")+"cdn.segment.com/analytics.js/v1/"+t+"/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(e,n)};analytics.SNIPPET_VERSION="3.1.0";
                analytics.load("<?= SEGMENT_API_KEY;?>");
                analytics.page()
            }}();
        </script>
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
           <div class="container-logged-user" ng-if="user.status === 'connected'">
                <span class="logged-user-name">Hi, {{user.name}}</span>
            </div>
            <div class="container-guest-user" ng-if="user.status != 'connected'">
                <span class="information-text">Login to save your progress</span>
                <a href = "./login">
                    <span class="button-blue button-sign-in">Join GameMash</span>
                </a>
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
                <div class="progress-bar">
                    <div class="fill-progress-bar" style="width: {{levelsUser.percentageGlobalCompletion}}%"></div>
                </div>
            </div>
            <div class="container-votes-statistics">
                You've votes {{user.nb_votes}} times.
                <br>
                <span class="login" ng-if="user.is_registered !== '1'">
                    Join GameMash to save your progress!
                </span>
                <span class="congrats" ng-if="user.is_registered === '1'">
                    {{user.status}}
                    Keep voting bro.
                </span>
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
    <!-- directives -->
    <script src="./assets/js/directives/backImgDirective.js"></script>
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