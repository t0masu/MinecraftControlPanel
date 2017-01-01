minecraftControlPanel.controller('dashboardController', ['dashboardService', function(dashboardService)
{
    var that = this;

    dashboardService.getDashboardCounters().then(function successCallback(data)
    {
        that.dashboardData = data.data;
    },
    function errorCallback()
    {
        that.dashboardData = {"error": true, "errorMessage": "Unable to load counters"};
    });
}]);
