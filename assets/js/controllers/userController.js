/**
 * Created by Personne on 11/10/2015.
 */
app.controller('userController', function ($scope, UserService, $rootScope) {

    $scope.user = {};

    $scope.status = 'not connected'

    $scope.initCurrentUser = function(){
        UserService.getCurrentUser()
            .success(function(data){
                $rootScope.$emit('updateUser', data.response.user);
                $rootScope.$emit('updateUserStatus', data.response.status);
                $scope.user.status = data.response.status;
            });
    };

    $scope.updateUser = function(args){
        $scope.user = args;
        analytics.identify(args.id,
            {
                name: args.name,
                is_registered: args.is_registered,
                first_seen_at: args.created,
                email: args.email,
                points: args.points,
                createdAt: args.registered_at,
                lastSeen: new Date()
            }
        );
    };

    $scope.initCurrentUser();

    $rootScope.$on('updateUser', function(event, args){
        $scope.updateUser(args);
    });

    $rootScope.$on('updateUserStatus', function(event, args){
        //console.log('new status : '+args);
        $scope.status = args;
    });
});