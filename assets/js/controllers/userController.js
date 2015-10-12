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
                analytics.identify(data.response.user.id,
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
                //console.log($scope.user);
            });
    };
    $scope.initCurrentUser();
});