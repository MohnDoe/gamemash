app.factory('User', function($http) {
    var User = function() {
        this.points = 0;
    };

    User.prototype.get = function() {
        return $http({
            url: 'api/user',
            method: 'GET',
            params: {page  : 0}
        })
            .success(function(data) {
                var user = data.user;
                this.points = user.points;
            }.bind(this));
    };
    return User;
});