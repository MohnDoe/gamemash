/**
 * Created by Personne on 11/10/2015.
 */
app.controller('userController', function ($scope, UserService) {

    $scope.user = {};

    $scope.initCurrentUser = function(){
        UserService.getCurrentUser()
            .success(function(data){
                $scope.user = data.user;
                $scope.status = data.status;
                console.log($scope.user);
            });
    };
    $scope.initCurrentUser();
});