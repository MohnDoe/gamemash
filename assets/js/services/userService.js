app.factory('UserService', function($http) {
    return {
        getCurrentUser: function(){
            return $http({
                url: 'api/user',
                method: 'GET'
            });
        },
        logout: function(){
            return $http({
                url: 'api/user/logout',
                method: 'POST'
            });
        }
    }
});