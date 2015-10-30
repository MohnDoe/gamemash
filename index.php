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
<!doctype html>
<html lang="fr">
    <head>
        <base href="<?= URL_BASE_HREF;?>">
        <title>GameMash</title>
        <meta content="text/html; charset=UTF-8" name="Content-Type" />
        <link rel = "stylesheet" href = "assets/stylesheets/main.css" />
        <!--    SEGMENT -->
        <script type="text/javascript">
            !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","page","once","off","on"];analytics.factory=function(t){return function(){var e=Array.prototype.slice.call(arguments);e.unshift(t);analytics.push(e);return analytics}};for(var t=0;t<analytics.methods.length;t++){var e=analytics.methods[t];analytics[e]=analytics.factory(e)}analytics.load=function(t){var e=document.createElement("script");e.type="text/javascript";e.async=!0;e.src=("https:"===document.location.protocol?"https://":"http://")+"cdn.segment.com/analytics.js/v1/"+t+"/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(e,n)};analytics.SNIPPET_VERSION="3.1.0";
                analytics.load("<?= SEGMENT_API_KEY;?>");
                analytics.page()
            }}();
        </script>
        <!--Start of HappyFox Live Chat Script-->
        <script>
            window.HFCHAT_CONFIG = {
                EMBED_TOKEN: "125dc360-79e3-11e5-b2ea-c7531f384798",
                ACCESS_TOKEN: "28c7ca88c65241708fe9637ceeed23d9",
                HOST_URL: "https://happyfoxchat.com",
                ASSETS_URL: "https://d1l7z5ofrj6ab8.cloudfront.net/visitor"
            };

            (function() {
                var scriptTag = document.createElement('script');
                scriptTag.type = 'text/javascript';
                scriptTag.async = true;
                scriptTag.src = window.HFCHAT_CONFIG.ASSETS_URL + '/js/widget-loader.js';

                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(scriptTag, s);
            })();
        </script>
        <!--End of HappyFox Live Chat Script-->
        <link rel="apple-touch-icon" sizes="57x57" href="./assets/favicons/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="./assets/favicons/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="./assets/favicons/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="./assets/favicons/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="./assets/favicons/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="./assets/favicons/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="./assets/favicons/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="./assets/favicons/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="./assets/favicons/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="./assets/favicons/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="./assets/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="./assets/favicons/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="./assets/favicons/favicon-16x16.png">
        <link rel="manifest" href="./assets/favicons/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="./assets/favicons/ms-icon-144x144.png">
        <meta name="theme-color" content="#1a2732">

        <meta property="og:url"                content="http://www.gamemash.net/" />
        <meta property="og:type"               content="website" />
        <meta property="og:title"              content="GameMash - Votez pour vos jeux préférés" />
        <meta property="og:description"        content="Choisissez le jeu que vous préférez parmi les deux jeux proposés." />
        <meta property="og:image"              content="http://www.gamemash.net/assets/img/gamemash-meta-img.jpg" />

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@GameMashHQ">
        <meta name="twitter:creator" content="@GameMashHQ">
        <meta name="twitter:title" content="GameMash - Votez pour vos jeux préférés">
        <meta name="twitter:description" content="Choisissez le jeu que vous préférez parmi les deux jeux proposés.">
        <meta name="twitter:image" content="http://www.gamemash.net/assets/img/gamemash-meta-img.jpg">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
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
                <li class="nav" ng-class="{active: $root.activetab == 'leaderboard'}">
                    <a href = "./leaderboard">Leaderboard</a>
                </li>
                <!---
                <li class="nav coming-soon disabled" ng-class="{active: $root.activetab == 'stats'}">
                    <a href = "./fight">Cool statistics</a>
                </li>
                --->
            </ul>
        </div>
        <div class="container-user" ng-controller="userController">
           <div class="container-logged-user" ng-if="status === 'connected'">
               <a href = "./profile/{{user.id}}">
                <span class="logged-user-name">
                {{user.name}}
                </span>
                <span class="logged-user-points">
                {{user.points | number}}
                </span>
               </a>
            </div>
            <div class="container-guest-user" ng-if="status != 'connected'">
                <span class="information-text">Connectez-vous pour sauvegarder votre progression</span>
                <a href = "./login">
                    <span class="button-blue button-sign-in">Rejoindre GameMash</span>
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
                Vous avez vot&eacute; {{user.nb_votes}} fois.
                <br>
                <span class="login" ng-if="user.is_registered !== '1'">
                    Inscrivez-vous pour sauvegarder votre progression !
                </span>
                <span class="congrats" ng-if="user.is_registered === '1'">
                    C'est beaucoup, bravo !
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
    <script src="./assets/js/controllers/leaderboardController.js"></script>
    <script src="./assets/js/controllers/profileController.js"></script>
    <!-- services -->
    <script src="./assets/js/services/gamemashStatisticsService.js"></script>
    <script src="./assets/js/services/topRankService.js"></script>
    <script src="./assets/js/services/userService.js"></script>

</html>