app.controller('fightController', function ($scope, $http, $rootScope, $timeout) {
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

    $scope.isBusy = false;

    $scope.currentUser = {
        left : {
            in_collection : false,
            in_wishlist : false,
            isBusy : false
        },
        right : {
            in_collection : false,
            in_wishlist : false,
            isBusy : false

        }
    }
    $scope.createFight = function (fight) {
        //console.log('creating fight...');
        $scope.isBusy = true;
        if(fight == null){
            $http.get('./api/fight').
                success(function (data, status, headers, config) {
                    $scope.fight = {
                        gameLeft : {
                            name : data.response.fight.gameLeft.name,
                            year : data.response.fight.gameLeft.year,
                            id : data.response.fight.gameLeft.id,
                            url_image : data.response.fight.gameLeft.url_image,
                            url_cover : data.response.fight.gameLeft.url_cover
                        },
                        gameRight : {
                            name : data.response.fight.gameRight.name,
                            year : data.response.fight.gameRight.year,
                            id : data.response.fight.gameRight.id,
                            url_image : data.response.fight.gameRight.url_image,
                            url_cover : data.response.fight.gameRight.url_cover
                        },
                        token : data.response.fight.token,
                        id : data.response.fight.id
                    };
                    $timeout(function(){$scope.isBusy = false;}, 400);

                    //console.log(data);
                }).
                error(function (data, status, headers, config) {
                    // called asynchronously if an error occurs
                    // or server returns response with an error status.
                });
        }else{
            $scope.fight = {
                gameLeft : {
                    name : fight.gameLeft.name,
                    year : fight.gameLeft.year,
                    id : fight.gameLeft.id,
                    url_image : fight.gameLeft.url_image,
                    url_cover : fight.gameLeft.url_cover
                },
                gameRight : {
                    name : fight.gameRight.name,
                    year : fight.gameRight.year,
                    id : fight.gameRight.id,
                    url_image : fight.gameRight.url_image,
                    url_cover : fight.gameRight.url_cover
                },
                token : fight.token,
                id : fight.id
            };
            $timeout(function(){$scope.isBusy = false;}, 150);
        }
    };

    $scope.voteFor = function(side){
        // vote stuff
        //console.log('voting for '+side);
        $scope.sendVoteFor(side);
    };

    $scope.addToCollection = function(side){
        console.log('adding game to collection ...');
        if(side === 'left'){
            $scope.currentUser.left.isBusy = true;
        }else if(side === 'right'){
            $scope.currentUser.right.isBusy = true;
        }else{
            return false;
        }
        console.log('adding game to collection ...');
        $http({
            url: 'api/game/add_collection',
            method: 'POST',
            data: 'side=' + side + '&id_fight=' + $scope.fight.id + '&token_fight=' + $scope.fight.token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
        }).
            success(function (data, status, headers, config) {
                if(data.status == 'OK'){
                    $scope.currentUser.right.isBusy = false;
                    $scope.currentUser.left.isBusy = false;
                    in_collection = data.response.action_results.in_collection;

                    if(in_collection){
                        analytics.track('Added game to collection',
                            {
                                id_game :  data.response.action_results.id_game,
                                side : data.response.action_results.side
                            });
                    }else{
                        analytics.track('Removed game from collection',
                            {
                                id_game :  data.response.action_results.id_game,
                                side : data.response.action_results.side
                            });
                    }
                    if(data.response.action_results.side == 'left'){
                        $scope.currentUser.left.in_collection = in_collection;
                    }else if(data.response.action_results.side == 'right'){
                        $scope.currentUser.right.in_collection = in_collection;
                    }else{
                        return false;
                    }
                }
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    };

    $scope.sendVoteFor = function(side){
        if($scope.isBusy){return;}
        $scope.isBusy = true;
        $http({
            url: 'api/vote',
            method: 'POST',
            data: 'side=' + side + '&id_fight=' + $scope.fight.id + '&token_fight=' + $scope.fight.token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
        }).
            success(function (data, status, headers, config) {
                if(data.status == 'OK'){
                    analytics.track('Vote');
                    $rootScope.$emit('updateUser', data.response.user);
                }
                // creating a new fight
                if(data.response.fight){
                    $scope.createFight(data.response.fight);
                }else{
                    $scope.createFight(null);
                }
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    };

    $scope.clearFighters = function(){
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
    };
    $scope.createFight(null);
});
