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
                        name : data.response.gameLeft.name,
                        year : data.response.gameLeft.year,
                        id : data.response.gameLeft.id,
                        url_image : data.response.gameLeft.url_image,
                        url_cover : data.response.gameLeft.url_cover
                    },
                    gameRight : {
                        name : data.response.gameRight.name,
                        year : data.response.gameRight.year,
                        id : data.response.gameRight.id,
                        url_image : data.response.gameRight.url_image,
                        url_cover : data.response.gameRight.url_cover
                    },
                    token : data.response.token,
                    id : data.response.id
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
