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

    $scope.isBusy = false;
    $scope.createFight = function () {
        console.log('creating fight...');
        $scope.isBusy = true;
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
                $scope.isBusy = false;
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
        if($scope.isBusy){return;}
        $scope.isBusy = true;
        $http({
            url: 'api/vote',
            method: 'POST',
            data: 'side=' + side + '&id_fight=' + $scope.fight.id + '&token_fight=' + $scope.fight.token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
        }).
            success(function (data, status, headers, config) {
                analytics.track('Vote');
                $rootScope.$emit('updateUser', data.response.user);
                $scope.isBusy = false;
                //$scope.clearFighters();

                //$rootScope.user.points = data['grand_total'];
                //console.log(data);
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
        console.log('clearing;');
    };
    $scope.createFight();
});
