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
        .when('/top', {
            templateUrl : 'assets/templates/top2.html',
            controller  : 'topController',
            activetab : 'top',
            title : 'Top 100'
        })
        .when('/login', {
            templateUrl : 'assets/templates/login.html',
            controller  : 'loginController',
            activetab : 'login',
            title : 'Join GameMash'
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