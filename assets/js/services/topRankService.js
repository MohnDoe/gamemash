app.factory('TopRank', function($http) {
    var TopRank = function() {
        this.games = [];
        this.busy = false;
        this.after = 1;
        this.limitReached = false;
    };

    TopRank.prototype.nextPage = function() {
        if (this.busy || this.limitReached) return;
        this.busy = true;

        $http({
            url: 'api/top',
            method: 'GET',
            params: {page  : this.after}
        })
            .success(function(data) {
                var games = data.response.games;
                for (var i = 0; i < games.length; i++) {
                    this.games.push(games[i]);
                }
                console.log(this.games);
                this.after = this.after + 1;
                if(this.after > 10){
                    this.limitReached = true;
                }
                this.busy = false;
            }.bind(this));
    };

    return TopRank;
});