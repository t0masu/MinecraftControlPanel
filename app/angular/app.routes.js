minecraftControlPanel.config(function($urlRouterProvider, $locationProvider, $stateProvider) {
    $urlRouterProvider
        .otherwise('/login'); //default route
    $locationProvider
        .html5Mode(true);
    $stateProvider
        .state('login', {
            name: 'login',
            url: '/login',
            templateUrl: '/app/views/login/login.tpl.html',
            controller: 'loginController',
            controllerAs: 'lC'
        })
});

minecraftControlPanel.run([
    '$rootScope',
    '$state',
    '$window',
    'Auth',
    function($rootScope, $state, $window, Auth) {
        $rootScope.$on('$stateChangeStart', function(e, to) {
            validToken = Auth.validToken();
            if (to.name == "login") {
                if (validToken == true) {
                    e.preventDefault();
                    $state.go('dashboard');
                    $state.reload();
                } else {
                    return;
                }
            } else {
                if (to.data.requireAuth && !validToken) {
                    e.preventDefault();
                    $state.go('login');
                }
            }
        });
    },
]);
