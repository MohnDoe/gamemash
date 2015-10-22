app.controller('topController', function ($scope, TopRank, UserService, $sce, $http) {

    $scope.nb_votes = false;
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

    $scope.getNumberVotes = function(){
        $http({
            url: 'api/stats',
            method: 'GET'
        })
        .success(function(data) {
                $scope.nb_votes = data.response.nb_votes;
        });
    }

    $scope.getNumberVotes();

});