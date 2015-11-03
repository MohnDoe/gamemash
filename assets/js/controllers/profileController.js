/**
 * Created by Personne on 29/10/2015.
 */
app.controller('profileController', function ($scope, $http, $routeParams, $location) {
    $scope.profile_id = $routeParams.userID;

    $scope.profile = {};

    $scope.isBusy = true;

    $scope.isEditable = false;

    $scope.isEditing = false;

    $scope.get_profile = function(){
        $scope.isBusy = true;
        $scope.isEditable = false;
        $http.get('./api/profile/'+$scope.profile_id).
            success(function (data, status, headers, config) {
                if(data.status == 'OK'){
                    $scope.profile = data.response.profile;
                    if(data.response.is_current_user){
                        $scope.editableProfile = $scope.profile;
                        $scope.isEditable = true;
                    }
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

    $scope.enable_editing = function(){
        $scope.isEditing = true;
    };

    $scope.save_editing = function(){
        $scope.isEditing = false;
    };

    $scope.cancel_editing = function(){
        $scope.isEditing = false;
    };

    $scope.get_last_votes();
    $scope.get_profile();

});
