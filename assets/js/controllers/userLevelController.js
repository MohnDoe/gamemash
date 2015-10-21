app.controller('levelUserController', function ($scope, $http, $rootScope, UserService){
    $scope.user = {};

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
        //console.log('getting levels ...');
        $http.get('./api/levels').
            then(function (result) {
                for (var i = 0; i < result.data.response.levels.length; i++) {
                    $scope.levelsUser.levels.push(result.data.response.levels[i]);
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
        //console.log($scope.user.points);
        //console.log($scope.levelsUser.previousAndPastLevels);
        pastLevel = $scope.levelsUser.previousAndPastLevels[0];
        nextLevel = $scope.levelsUser.previousAndPastLevels[1];
        if($scope.levelsUser.previousAndPastLevels[0] === undefined || $scope.levelsUser.previousAndPastLevels[1] === undefined)
        {
            completion = 100;
        }else
        {
            completion = ($scope.user.points - pastLevel.needed) / ( nextLevel.needed - pastLevel.needed ) * 100;
        }
        $scope.levelsUser.percentageGlobalCompletion = completion;
    };

    $scope.getUserPoints = function(){
        //console.log('getting user points ...');
        UserService.getCurrentUser()
            .success(function(data){
                $scope.user = data.response.user;
                aLevels = $scope.getCurrentLevel();
                $scope.levelsUser.previousAndPastLevels = aLevels;
            });
    };

    $scope.updateUserPoints = function(){
        //$scope.user.points = points_array['grand_total'];
        previousAndPastLevels = $scope.getCurrentLevel();
        $scope.levelsUser.previousAndPastLevels = previousAndPastLevels;
        $scope.refreshCurrentProgress();

        //shake the user points indicator
        $('.current-points').removeClass('shake');
        setTimeout(function () {
            $('.current-points').addClass('shake');
        }, 100);
    };

    $scope.$watchCollection("levelsUser.previousAndPastLevels",
        function(newValue, oldValue){
            //console.log(newValue);
            if(typeof newValue != 'undefined' && newValue.length > 0){
                $scope.refreshCurrentProgress();
            }
        }
    );

    $rootScope.$on('updateUser', function(event, args){
        $scope.user = args;
        $scope.updateUserPoints();

    });


    $scope.getLevels();

    //$interval(function(){$scope.refresh();}, 10000);
});