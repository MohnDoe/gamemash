/**
 * Created by Personne on 29/10/2015.
 */
app.controller('profileController', function ($scope, $http, $routeParams, $location) {
    $scope.profile_id = $routeParams.userID;

    $scope.profile = {};

    $scope.isBusy = true;

    $scope.get_profile = function(){
        $scope.isBusy = true;
        $http.get('./api/profile/'+$scope.profile_id).
            success(function (data, status, headers, config) {
                console.log(data);
                if(data.status == 'OK'){
                    $scope.profile = data.response.profile;
                }else{
                    $location.path('/');
                }
                $scope.isBusy = false;
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    };

    $scope.get_last_votes = function(){
        $http.get('./api/user/'+$scope.profile_id+'/last_votes').
            success(function (data, status, headers, config) {
                if(data.status == 'OK'){
                    $scope.last_votes = data.response.last_votes;
                }
                console.log(data);
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    };

    $scope.get_last_votes();
    $scope.get_profile();
});
