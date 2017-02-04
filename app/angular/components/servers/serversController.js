minecraftControlPanel.controller('serversController', ['serversService', '$uibModal', function(serversService, $uibModal)
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
        $uibModal.open({
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

minecraftControlPanel.controller("createServerModalController", ['serversService', '$uibModalInstance', '$http', '$timeout', function(serversService, $uibModalInstance, $http, $timeout)
{
    //init pages
    var that = this;
    this.initMenu = true;
    this.autocreateForm = false;
    this.standaloneForm = false;

    this.showSpinner = false;
    this.success = false;
    this.error = false;

    this.autocreateSubmit = function(data)
    {
        that.autocreateForm = false;
        that.showSpinner = true;

        serversService.createServerOnHost(data).then(function successCallback(output)
        {
            console.log(output);
            if(output.data.success == true)
            {
                $timeout(function()
                {
                    that.showSpinner = false;
                    that.success = true;
                }, 750);
                $timeout(function()
                {
                    $uibModalInstance.close('refresh');
                }, 2500);
            }
            else
            {
                that.error = true;
            }
        }, function errorCallback(output) {
            if(output.error == true)
            {
                that.error = true;
            }
        });
    }

    //autocreateForm = true; initMenu = false;
    this.fetchAutocreateData = function()
    {
        $http.get("/api/minecraft/versions")
        .then(function(data) {
            that.minecraftVersionData = data.data;
        }, function() {
            that.autocreateForm = false;
            that.error = true;
        });

        $http.get("/api/hosts/all").then(function(data) {
            that.hostsData = data.data;
            that.initMenu = false;
            that.autocreateForm = true;
        }, function() {
            that.autocreateForm = false;
            that.error = true;
        });
    }

    this.close = function() {
        $uibModalInstance.dismiss();
    };

}]);
