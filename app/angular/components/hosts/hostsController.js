minecraftControlPanel.controller('hostsController', ['hostsService', '$uibModal', function(hostsService, $uibModal) {
    var that = this;

    hostsService.getUserHosts().then(function successCallback(data)
    {
        that.hostsData = data.data;
    }, function errorCallback(data)
    {
        that.hostsData = [{"hostName": "Unable to load hosts"}];
    });

    this.createHost = function() {
        $uibModal.open({
            animation: true,
            size: 'md',
            controller: function($uibModalInstance, $timeout) {
                //init pages
                var that = this;
                this.showForm = true;
                this.showSpinner = false;
                this.success = false;
                this.error = false;

                this.submit = function(data)
                {
                    that.showForm = false;
                    that.showSpinner = true;

                    hostsService.createHost(data).then(function successCallback(output)
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

                this.close = function() {
                    $uibModalInstance.dismiss();
                }
            },
            controllerAs: 'cHC',
            templateUrl: '/app/views/hosts/createHostModal.tpl.html'
        }).result.then(function(action)
        {
            if(action == 'refresh')
            {
                hostsService.getUserHosts().then(function successCallback(data)
                {
                    that.hostsData = data.data;
                }, function errorCallback(data)
                {
                    that.hostsData = [{"hostName": "Unable to load hosts"}];
                });
            }
        });
    };

    this.rebootHosts = function() {
        $uibModal.open({
            animation: true,
            size: 'md',
            controller: function($uibModalInstance) {
                this.close = function() {
                    $uibModalInstance.dismiss();
                }
            },
            controllerAs: 'cHC',
            templateUrl: '/app/views/hosts/createHostModal.tpl.html'
        });
    };

    this.stopHosts = function() {
        $uibModal.open({
            animation: true,
            size: 'md',
            controller: function($uibModalInstance) {
                this.close = function() {
                    $uibModalInstance.dismiss();
                }
            },
            controllerAs: 'cHC',
            templateUrl: '/app/views/hosts/createHostModal.tpl.html'
        });
    };

}]);
