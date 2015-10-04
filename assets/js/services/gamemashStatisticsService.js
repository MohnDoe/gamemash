app.factory('GameMashStatistics', function($http) {
    var GameMashStatistics = function() {
        this.stats = [];
    };

    GameMashStatistics.prototype.get = function() {

        $http({
            url: 'api/stats',
            method: 'GET'
        })
            .success(function(data) {
                this.stats = data;
            }.bind(this));
    };

    return GameMashStatistics;
});