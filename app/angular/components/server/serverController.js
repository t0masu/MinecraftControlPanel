minecraftControlPanel.controller('serverController', ['$stateParams', 'serversService', 'Server', '$timeout',function($stateParams, serversService, Server, $timeout)
{
    var that = this;
    var status = false;

    //cache the id in factory
    Server.init($stateParams.serverId);

    Server.status().then(function(data)
    {
        that.statusData = data.data;
    });

    //load server information
    serversService.fetchServerData($stateParams.serverId).then(function(data)
    {
        that.serverData = data.data;
    }, function()
    {
        that.serverData = {};
    });

    //download backup
    this.download = function(index)
    {

    };

    //backup server
    this.backup = function()
    {

    }

    //start server
    this.start = function()
    {
        Server.start();
        that.statusData.status = "Starting Up";
        $timeout(function () {
            Server.status().then(function(data)
            {
                that.statusData = data.data;
            });
        }, 13000);
    };

    //reboot server
    this.reboot = function()
    {
        Server.reboot();
        that.statusData.status = "Rebooting";
        $timeout(function () {
            Server.status().then(function(data)
            {
                that.statusData = data.data;
            });
        }, 26000);
    };

    //stop server
    this.stop = function()
    {
        Server.stop();
        that.statusData.status = "Shutting Down ";
        $timeout(function () {
            Server.status().then(function(data)
            {
                that.statusData = data.data;
            });
        }, 13000);
    };

    this.status = function()
    {
        Server.status().then(function(data)
        {
            that.statusData = data.data;
        });
    }
}]);


minecraftControlPanel.factory('Server', ['$http', function($http)
{
    var cachedId;

    return {
        init: init,
        start: start,
        reboot: reboot,
        stop: stop,
        backup: backup,
        download: download,
        status: status
    };

    function init(serverId)
    {
        if(!cachedId)
        {
            cachedId = serverId;
        }
    }

    function start()
    {
        return $http.post('/api/servers/startServer', {"id": cachedId});
    }

    function reboot()
    {
        return $http.post('/api/servers/rebootServer', {"id": cachedId});
    }

    function stop()
    {
        return $http.post('/api/servers/stopServer', {"id": cachedId});
    }

    function backup()
    {
        return $http.post('/api/servers/backupServer', {"id": cachedId});
    }

    function download(index)
    {

    }

    function status()
    {
        return $http.post('/api/minecraft/getStatus', {"id": cachedId});
    }
}]);
