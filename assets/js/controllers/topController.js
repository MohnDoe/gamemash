app.controller('topController', function ($scope, TopRank, UserService) {

    $scope.top = new TopRank();
    $scope.canSee = false;
    $scope.error = 'Chargement...';
    $scope.isBusy = true;
    UserService.getCurrentUser()
    .success(function(data){
            if(data.response.status === 'connected'){
                $scope.canSee = true;
            }else{
                $scope.canSee = false;
                $scope.error = 'Vous devez etre connecte pour acceder au classement des jeux'
            }
            $scope.isBusy = false;
        });

});