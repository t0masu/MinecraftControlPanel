var minecraftControlPanel = angular.module('minecraftControlPanel',
[
    'ui.router', //ui router from angular-ui
    'ui.bootstrap',
    'ngAnimate'
]);

minecraftControlPanel.run(['$rootScope', '$state', function($rootScope, $state)
{
    $rootScope.$state = $state;
}]);

minecraftControlPanel.controller('topLevelController', ['Auth', '$rootScope', function(Auth, $rootScope)
{
    var that = this;

    this.tokenData = Auth.decodeJWT; //fallback on refresh
    $rootScope.$on('userLogin', function(event, data) {
        that.tokenData = data; //after user login (fake page refresh)
    });
}]);
