var minecraftControlPanel = angular.module('minecraftControlPanel',
[
    'ui.router' //ui router from angular-ui
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

mgrApp.controller('loginCtrl', [
    'loginService',
    'Auth',
    '$state',
    '$timeout',
    '$rootScope',
    function(loginService, Auth, $state, $timeout, $rootScope) {
        var that = this;
        this.form = [];
        this.submit = function(data) {
            //pass login info to loginService
            loginService.parseLogin(data).then(function successCallback(res) {
                console.log(res);
                if (res.data.success === true) {
                    Auth.setToken(res.data.token);
                    that.errorText = 'Logging you in.';

                    $timeout(function() {
                        $state.go('dashboard');
                        //decode token
                        var token_parts = res.data.token.split('.');
                        var output = angular.fromJson(atob(token_parts[1]))._doc;
                        $rootScope.$emit('userLogin', output);
                    }, 1500);
                } else {
                    that.errorText = res.data.message;
                }
            }, function errorCallback() {
                that.errorText = 'Please check your details and try again.';
            });
        };
    }
]);

mgrApp.controller('navController', function(Auth, $rootScope, $mdSidenav, $scope, $state) {
    var that = this;

    this.tokenData = Auth.decodeJWT; //fallback on refresh
    $rootScope.$on('userLogin', function(event, data) {
        that.tokenData = data; //after user login (fake page refresh)
    });

    this.logout = function() {
        Auth.clearToken();
        $state.reload();
        $state.go('login');
    }

    $scope.toggleMenu = function(menu) {
        $mdSidenav(menu).toggle();
    };
});
