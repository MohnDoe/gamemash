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
            templateUrl : 'assets/templates/top.html',
            controller  : 'topController',
            activetab : 'top',
            title : 'Top 100'
        });

        $locationProvider.html5Mode(true);
});

app.controller('fightController', function ($scope, $http, $rootScope) {

    $scope.fight = {
        gameLeft : {
            name : '',
            year : '',
            id : '',
            url_image : '',
            url_cover : ''
        },
        gameRight : {
            name : '',
            year : '',
            id : '',
            url_image : '',
            url_cover : ''
        },
        token : '',
        id : 0
    };
    $scope.createFight = function () {
        console.log('creating fight...');
        $http.get('./api/fight').
            success(function (data, status, headers, config) {
                $scope.fight = {
                    gameLeft : {
                        name : data.gameLeft.name,
                        year : data.gameLeft.year,
                        id : data.gameLeft.id,
                        url_image : data.gameLeft.url_image,
                        url_cover : data.gameLeft.url_cover
                    },
                    gameRight : {
                        name : data.gameRight.name,
                        year : data.gameRight.year,
                        id : data.gameRight.id,
                        url_image : data.gameRight.url_image,
                        url_cover : data.gameRight.url_cover
                    },
                    token : data.token,
                    id : data.id
                };
                //console.log(data);
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    };

    $scope.voteFor = function(side){
        // vote stuff
        console.log('voting for '+side);
        $scope.sendVoteFor(side);

        // creating a new fight
        $scope.createFight();
    };

    $scope.sendVoteFor = function(side){
        $http({
            url: 'api/vote',
            method: 'POST',
            data: 'side=' + side + '&id_fight=' + $scope.fight.id + '&token_fight=' + $scope.fight.token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
        }).
            success(function (data, status, headers, config) {
                $rootScope.$emit('userGetPoints', data);
                //$rootScope.user.points = data['grand_total'];
                //console.log(data);
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    };

    $scope.createFight();
});

/*
app.controller('userController', function ($scope, $http, $rootScope) {

    $scope.user = {
        points : 0,
        level : {
            current : {
                name : "Level 2",
                scarcity : 'legend',
                needed : 5340
            },
            next : {
                name : "Level 3",
                scarcity : 'ultra',
                needed : 100000
            },
            completion : 0
        }
    };

    $scope.$watch('user.points', function(newValue){
        $scope.user.level.completion =  newValue * 100 / $scope.user.level.next.needed;
        //console.log($scope.user.level);
    });

    $scope.getUser = function () {
        $http.get('./api/user').
            success(function (data, status, headers, config) {
                $scope.user = {
                    points: data.user.points
                };
                //console.log(data);
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    };
    //$scope.getUser();
    $rootScope.user = $scope.user;

});
*/

app.controller('topController', function ($scope, TopRank) {

    $scope.top = new TopRank();

});

app.factory('TopRank', function($http) {
    var TopRank = function() {
        this.games = [];
        this.busy = false;
        this.after = 1;
        this.limitReached = false;
    };

    TopRank.prototype.nextPage = function() {
        if (this.busy || this.limitReached) return;
        this.busy = true;

        $http({
            url: 'api/top',
            method: 'GET',
            params: {page  : this.after}
        })
            .success(function(data) {
                var games = data.games;
                for (var i = 0; i < games.length; i++) {
                    this.games.push(games[i]);
                }
                this.after = this.after + 1;
                if(this.after >= 10){
                    this.limitReached = true;
                }
                this.busy = false;
            }.bind(this));
    };

    return TopRank;
});

app.controller('levelUserController', function ($scope, $http, $rootScope, $animate){
    $scope.user = {
        points : null
    };

    $scope.levelsUser = {
        percentageGlobalCompletion : "0",
        levels : [
            {
                name : 'Noob',
                needed : -1
            }
        ],
        previousAndPastLevels : []
    };

    $scope.getLevels = function(){
        console.log('getting levels ...');
        $http.get('./api/levels').
            then(function (result) {
                console.log(result.data.levels);
                for (var i = 0; i < result.data.levels.length; i++) {
                    $scope.levelsUser.levels.push(result.data.levels[i]);
                }
                //$scope.levelsUser.levels = result.data.levels;
                $scope.getUserPoints();
            });
    };
    $scope.getCurrentLevel = function(){
        //console.log('getting current level...')
        //console.log('user points : '+$scope.user.points);
        if($scope.user.points === null){return;}
        for(i = 0; i < $scope.levelsUser.levels.length; i++){
            level = $scope.levelsUser.levels[i];
            if(level.needed >= $scope.user.points ){
                pastLevel = $scope.levelsUser.levels[i-1];
                nextLevel = $scope.levelsUser.levels[i];
                return [pastLevel, nextLevel];
            }
        }
        return [$scope.levelsUser.levels[0],$scope.levelsUser.levels[1]];
    };

    $scope.refreshCurrentProgress = function(){
        pastLevel = $scope.levelsUser.previousAndPastLevels[0];
        nextLevel = $scope.levelsUser.previousAndPastLevels[1];
        completion = ($scope.user.points - pastLevel.needed) / ( nextLevel.needed - pastLevel.needed ) * 100;
        $scope.levelsUser.percentageGlobalCompletion = completion;
    };

    $scope.getUserPoints = function(){
        console.log('getting user points ...');
        $http.get('./api/user/points').
            then(function (result) {
                //console.log('result from GET ./api/user', result);
                $scope.user.points = result.data.user.points;
                console.info('user has '+$scope.user.points+' points.');
                aLevels = $scope.getCurrentLevel();
                $scope.levelsUser.previousAndPastLevels = aLevels;
            });
    };

    $scope.updateUserPoints = function(points_array){
        $scope.user.points = points_array['grand_total'];
        $scope.levelsUser.previousAndPastLevels = $scope.getCurrentLevel();
        $scope.refreshCurrentProgress();
        $animate.addClass($('.current-points'), 'shake').then(function() {
            $('.current-points').removeClass('shake');
        });
    }

    $scope.$watchCollection("levelsUser.previousAndPastLevels",
        function(newValue, oldValue){
            console.log(newValue);
            if(typeof newValue != 'undefined' && newValue.length > 0){
                $scope.refreshCurrentProgress();
            }
        }
    );

    $rootScope.$on('userGetPoints', function(event, args){
        console.info('event received userGetPoints.');
        $scope.updateUserPoints(args);
    });


    $scope.getLevels();

    //$interval(function(){$scope.refresh();}, 10000);
});

app.factory('User', function($http) {
    var User = function() {
        this.points = 0;
    };

    User.prototype.get = function() {
        return $http({
            url: 'api/user',
            method: 'GET',
            params: {page  : 0}
        })
            .success(function(data) {
                var user = data.user;
                this.points = user.points;
            }.bind(this));
    };
    return User;
});

app.factory('GameMashStatistics', function($http) {
    var GameMashStatistics = function() {
        this.stats = [];
    };

    GameMashStatistics.prototype.get = function() {

        $http({
            url: 'api/stats',
            method: 'GET'
        })
            .success(function(data) {
                this.stats = data;
            }.bind(this));
    };

    return GameMashStatistics;
});

app.run(['$rootScope', function($rootScope){
    $rootScope.$on('$routeChangeStart', function(event, next, current){
        $rootScope.activetab = next.$$route.activetab;
        document.title = 'GameMash - '+next.$$route.title;
    });
}]);