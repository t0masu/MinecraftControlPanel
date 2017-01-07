minecraftControlPanel.controller('accountController', ['accountService', '$uibModal', 'Auth', function(accountService, $uibModal, Auth)
{
    var that = this;

    accountService.getAccountData().then(function(data)
    {
        that.accountData = data.data;
    });

    this.changePassword = function()
    {
        $uibModal.open({
            animations: true,
            templateUrl: "/app/views/account/changePasswordModal.tpl.html",
            controller: "changePasswordModalController",
            controllerAs: "cPMC"
        }).result.then(function(formData)
        {
            accountService.changePassword(formData).then(function(outcome)
            {
                if(outcome.data.success == true)
                {
                    location.href="/logout";
                }
            });
        })
    };

    this.linkAccount = function()
    {
        $uibModal.open({
            animations: true,
            templateUrl: "/app/views/account/linkAccountModal.tpl.html",
            controller: "linkAccountModalController",
            controllerAs: "lAMC"
        }).result.then(function(output)
        {
            if(output.data.success == true)
            {
                accountService.getAccountData().then(function(data)
                {
                    that.accountData = data.data;
                });
            }
        });
    };

    this.unlinkAccount = function()
    {
        $uibModal.open({
            animations: true,
            templateUrl: "/app/views/account/unlinkAccountModal.tpl.html",
            controller: "unlinkAccountModalController",
            controllerAs: "uAMC"
        }).result.then(function(result)
        {
            if(result.data.success == true)
            {
                accountService.unlinkAccount().then(function()
                {
                    accountService.getAccountData().then(function(data)
                    {
                        that.accountData = data.data;
                    });
                });
            }
            else
            {
                return;
            }
        })
    };
}]);

minecraftControlPanel.controller('changePasswordModalController', ['$uibModalInstance', function($uibModalInstance)
{
    var that = this;

    this.cancel = function()
    {
        $uibModalInstance.close();
    };

    this.submit = function(data)
    {
        $uibModalInstance.close(data);
    };
}]);

minecraftControlPanel.controller('linkAccountModalController', ['$uibModalInstance', 'accountService', function($uibModalInstance, accountService)
{
    var that = this;

    this.cancel = function()
    {
        $uibModalInstance.close();
    };

    this.submit = function(data)
    {
        accountService.linkAccount(data).then(function(outcome)
        {
            $uibModalInstance.close(outcome);
        });
    };
}]);

minecraftControlPanel.controller('unlinkAccountModalController', ['$uibModalInstance', 'accountService', function($uibModalInstance, accountService)
{
    var that = this;

    this.cancel = function()
    {
        $uibModalInstance.close();
    };

    this.submit = function(confirm)
    {
        if(confirm == true)
        {
            accountService.unlinkAccount().then(function(outcome)
            {
                $uibModalInstance.close(outcome);
            });
        }
        else
        {
            $uibModalInstance.close(confirm);
        }
    };
}]);
