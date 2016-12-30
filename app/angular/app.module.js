var minecraftControlPanel = angular.module('minecraftControlPanel',
[
    'ui.router' //ui router from angular-ui
]);

minecraftControlPanel.run(['$rootScope', '$state', function($rootScope, $state)
{
    $rootScope.$state = $state;
}]);

minecraftControlPanel.controller('topLevelController', [function()
{
    var that = this;

}]);
