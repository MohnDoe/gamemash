/**
 * Created by Personne on 10/10/2015.
 */
app.controller('loginController', function ($scope, $http, $location) {

    $scope.register = function(){
        $http({
            url: 'api/user/register',
            method: 'POST',
            data: 'email=' + $scope.user.email + '&password=' + $scope.user.password,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
        }).
            success(function (data, status, headers, config) {
                if(data.status != 'not connected'){
                    $location.path('/');
                }
                console.log(data);
                $rootScope.user = data.user;
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    };
});
