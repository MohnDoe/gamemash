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
                if(data.response.status != 'not connected'){
                    if(data.response.status == 'registered'){
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
                    }else if(data.response.status == 'connected'){
                        analytics.identify(data.response.user.id);
                        analytics.track('Logged In');
                    }
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
