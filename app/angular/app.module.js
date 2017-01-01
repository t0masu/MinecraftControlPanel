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

//Request interceptor
minecraftControlPanel.factory('authInterceptor', function($rootScope, $q, Auth) {
    var sessionInjector = {
        request: function(config) {
            var token = Auth.getToken();
            if (token) {
                config.headers = config.headers || {};
                config.headers['x-access-token'] = token;
            }

            return config;
        }
    };
    return sessionInjector;
});

minecraftControlPanel.config([
    '$httpProvider',
    function($httpProvider) {
        $httpProvider.interceptors.push('authInterceptor');
    }
]);

minecraftControlPanel.controller('topLevelController', ['Auth', '$rootScope', function(Auth, $rootScope)
{
    var that = this;

    this.tokenData = Auth.decodeJWT; //fallback on refresh
    $rootScope.$on('userLogin', function(event, data) {
        that.tokenData = data; //after user login (fake page refresh)
    });

    this.sidebar = false; //init for sidebar to be closed
}]);
