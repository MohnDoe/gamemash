/**
 * Created by Personne on 17/09/2015.
 */
var app = angular.module('appGameMash', ['ngRoute', 'infinite-scroll']);
/*
app.config(function ($httpProvider) {
    $httpProvider.defaults.transformRequest = function (data) {
        if (data === undefined) {
            return data;
        }
        return $.param(data);
    };
    $httpProvider.defaults.headers.post['Content-Type'] = '' + 'application/x-www-form-urlencoded; charset=UTF-8';
});
*/
app.config(function($routeProvider, $locationProvider) {
    $routeProvider
        // route for the home page
        .when('/', {
            templateUrl : 'assets/templates/fight.html',
            controller  : 'fightController',
            activetab : 'fight',
            title : 'Vote'
        })
        .when('/fight', {
            templateUrl : 'assets/templates/fight.html',
            controller  : 'fightController',
            activetab : 'fight',
            title : 'Vote'
        })
        .when('/leaderboard', {
            templateUrl : 'assets/templates/leaderboard.html',
            controller  : 'leaderboardController',
            activetab : 'leaderboard',
            title : 'Ceux qui pesent dans le game'
        })
        .when('/top', {
            templateUrl : 'assets/templates/top.html',
            controller  : 'topController',
            activetab : 'top',
            title : 'Top 100'
        })
        .when('/profile/:userID', {
            templateUrl : 'assets/templates/profile.html',
            controller  : 'profileController',
            activetab : 'profile',
            title : 'Votre profilwa'
        })
        .when('/login', {
            templateUrl : 'assets/templates/login.html',
            controller  : 'loginController',
            activetab : 'login',
            title : 'Rejoindre GameMash'
        });

        $locationProvider.html5Mode(true);
});

app.filter('setDecimal', function(){
    return function (input, places) {
        if (isNaN(input)) return input;
        // If we want 1 decimal place, we want to mult/div by 10
        // If we want 2 decimal places, we want to mult/div by 100, etc
        // So use the following to create that factor
        var factor = "1" + Array(+(places > 0 && places + 1)).join("0");
        return Math.round(input * factor) / factor;
    };
});

app.run(['$rootScope', '$location', 'UserService', function($rootScope, $location, UserService){
    console.log(
        '%c v. 1.0 \'Reborn\' \n'
        , 'color: #feb41c; background: #24221f; font-weight:bold;'
    );
    console.log(
        '%c Entirely coded & designed by Kevin F. Poivre \n' +
        ' Lovingly hand-crafted in Nancy, France \n\n'+
        ' kevin@gamemash.net \n'+
        ' https://twitter.com/GameMashHQ \n'+
        ' https://www.facebook.com/GameMashHQ \n'
        , 'background: #feb41c; color: #24221f'
    );

    console.log(
        '%c https://youtu.be/k78OjoJZcVc \n'
        , 'color: #f1f1f1; background: #cc181e; font-weight:bold;'
    );

    $rootScope.$on('$routeChangeStart', function(event, next, current){
        UserService.getCurrentUser()
            .success(function(data){
                if(data.response.status == 'connected' && $location.path() == '/login'){
                    $location.path('/');
                }
            });
        $rootScope.activetab = next.$$route.activetab;
        document.title = 'GameMash - '+next.$$route.title;
    });
}]);