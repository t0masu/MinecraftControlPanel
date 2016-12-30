minecraftControlPanel.controller('hostsController', ['$uibModal', function($uibModal)
{
    var that = this;

    this.createHost = function()
    {
        $uibModal.open({
            animation: true,
            size: 'md',
            controller: function($uibModalInstance){
                this.close = function(){ $uibModalInstance.dismiss(); }
            },
            controllerAs: 'cHC',
            templateUrl: '/app/views/hosts/createHostModal.tpl.html'
        });
    }

    this.rebootHosts = function()
    {
        $uibModal.open({
            animation: true,
            size: 'md',
            controller: function($uibModalInstance){
                this.close = function(){ $uibModalInstance.dismiss(); }
            },
            controllerAs: 'cHC',
            templateUrl: '/app/views/hosts/createHostModal.tpl.html'
        });
    }

    this.stopHosts = function()
    {
        $uibModal.open({
            animation: true,
            size: 'md',
            controller: function($uibModalInstance){
                this.close = function(){ $uibModalInstance.dismiss(); }
            },
            controllerAs: 'cHC',
            templateUrl: '/app/views/hosts/createHostModal.tpl.html'
        });
    }

}]);
