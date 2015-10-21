app.controller('topController', function ($scope, TopRank, UserService, $sce) {

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
                $scope.error = $sce.trustAsHtml("Vous devez etre connecté pour acceder au classement");
            }
            $scope.isBusy = false;
        });

});