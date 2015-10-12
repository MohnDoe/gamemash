/**
 * Created by Personne on 11/10/2015.
 */
app.controller('userController', function ($scope, UserService) {

    $scope.user = {};

    $scope.initCurrentUser = function(){
        UserService.getCurrentUser()
            .success(function(data){
                $scope.user = data.response.user;
                $scope.user.status = data.response.status;
                console.log($scope.user);
            });
    };
    $scope.initCurrentUser();
});