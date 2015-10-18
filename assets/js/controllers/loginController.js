/**
 * Created by Personne on 10/10/2015.
 */
app.controller('loginController', function ($scope, $http, $location, $rootScope) {

    $scope.register = function(){
        $scope.register_error_message = '';

        $http({
            url: 'api/user/register',
            method: 'POST',
            data: 'email=' + $scope.userRegister.email + '&name=' + $scope.userRegister.name + '&password=' + $scope.userRegister.password,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
        }).
            success(function (data, status, headers, config) {
                console.log(data);
                if(data.response.status == 'connected'){
                        analytics.alias(data.response.user.id);
                        analytics.identify(
                            data.response.user.id,
                            {
                                name: data.response.user.name,
                                is_registered: data.response.user.is_registered,
                                registered_at: data.response.user.registered_at,
                                email: data.response.user.email,
                                points: data.response.user.points,
                                createdAt: data.response.user.created,
                                lastSeen: data.response.user.last_seen
                            }
                        );
                        analytics.track('Signed Up');
                        $rootScope.$emit('updateUser', data.response.user);
                        $rootScope.$emit('updateUserStatus', data.response.status);
                        $location.path('/');
                }else{
                    if(data.response.error_message != ''){
                        $scope.register_error_message = data.response.error_message;
                    }
                }
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    };
    $scope.login = function(){
        $scope.login_error_message = '';
        $http({
            url: 'api/user/login',
            method: 'POST',
            data: 'email=' + $scope.userLogin.email + '&password=' + $scope.userLogin.password,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
        }).
            success(function (data, status, headers, config) {
                if(data.response.status == 'connected'){
                    analytics.identify(data.response.user.id);
                    analytics.track('Logged In');
                    $rootScope.$emit('updateUser', data.response.user);
                    $rootScope.$emit('updateUserStatus', data.response.status);
                    $location.path('/');
                }else{
                    if(data.response.error_message != ''){
                        $scope.login_error_message = data.response.error_message;
                    }
                }
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
    };
});
