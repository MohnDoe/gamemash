/**
 * Created by Personne on 20/10/2015.
 */
app.controller('leaderboardController', function ($scope, $http) {
    $scope.leaderboards = {
        all : {
            content: {},
            isBusy: true
        },
        month : {
            content: {},
            isBusy: true
        },
        week : {
            content: {},
            isBusy: true
        }
    };

    $scope.getLeaderboard = function (period) {
        if(period == 'all')
        {
            $scope.leaderboards.all.isBusy = true;
        }
        else if(period == 'month')
        {
            $scope.leaderboards.month.isBusy = true;
        }
        else if(period == 'week')
        {
            $scope.leaderboards.week.isBusy = true;
        }else
        {
            return false;
        }
        $http.get('./api/leaderboard/'+period).
            success(function (data, status, headers, config) {
                if(period == 'all')
                {
                    $scope.leaderboards.all.content = data.response.leaderboard;
                    $scope.leaderboards.all.isBusy = false;
                }
                else if(period == 'month')
                {
                    $scope.leaderboards.month.content = data.response.leaderboard;
                    $scope.leaderboards.month.isBusy = false;
                }
                else if(period == 'week')
                {
                    $scope.leaderboards.week.content = data.response.leaderboard;
                    $scope.leaderboards.week.isBusy = false;
                }
                //console.log(period);
                //console.log(data);
            }).
            error(function (data, status, headers, config) {
                // called asynchronously if an error occursmain
                // or server returns response with an error status.
            });
    };

    $scope.getAllLeaderboards = function(){
        $scope.getLeaderboard('all');
        $scope.getLeaderboard('month');
        $scope.getLeaderboard('week');
    }

    $scope.getAllLeaderboards();
});