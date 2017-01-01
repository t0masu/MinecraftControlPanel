minecraftControlPanel.controller('serversController', ['serversService', '$uibModalInstance', function(serversService, $uibModalInstance)
{
    var that = this;

    serversService.getUserServers().then(function successCallback(data)
    {
        that.serverData = data.data;
    }, function errorCallback(data)
    {
        that.serverData = [{"serverName": "Unable to load servers"}];
    });

    this.createServer = function()
    {
        $uibModalInstance.open({
            animations: true,
            templateUrl: "/app/views/servers/createServerModal.tpl.html",
            controller: "createServerModalController",
            controllerAs: "cSMC"
        }).result.then(function(output)
        {
            if(output == 'refresh')
            {
                serversService.getUserServers().then(function successCallback(data)
                {
                    that.serverData = data.data;
                }, function errorCallback(data)
                {
                    that.serverData = [{"serverName": "Unable to load servers"}];
                });
            }
        });
    };
}]);

minecraftControlPanel.controller("createServerModalController", ['serversService', '$uibModalInstance', function(serversService, $uibModalInstance)
{
    var that = this;

    this.close = function() {
        $uibModalInstance.dimiss();
    };

    this.submit = function() {
        //
    };
}]);
