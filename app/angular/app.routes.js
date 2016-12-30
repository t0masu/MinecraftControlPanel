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
        .state('logout', {
            name: 'logout',
            url: '/logout',
            controller: 'logoutController',
            controllerAs: 'lC'
        })
        .state('dashboard', {
            name: 'dashboard',
            url: '/dashboard',
            templateUrl: '/app/views/dashboard/dashboard.tpl.html',
            controller: 'dashboardController',
            controllerAs: 'dC'
        })
        .state('dashboard.hosts', {
            name: 'hosts',
            url: '/hosts',
            templateUrl: '/app/views/hosts/hosts.tpl.html',
            controller: 'hostsController',
            controllerAs: 'hC'
        })
        .state('dashboard.servers', {
            name: 'servers',
            url: '/servers',
            templateUrl: '/app/views/servers/servers.tpl.html',
            controller: 'serversController',
            controllerAs: 'sC'
        })
        .state('dashboard.backups', {
            name: 'backups',
            url: '/backups',
            templateUrl: '/app/views/backups/backups.tpl.html',
            controller: 'backupsController',
            controllerAs: 'bC'
        })
        .state('dashboard.plugins', {
            name: 'plugins',
            url: '/plugins',
            templateUrl: '/app/views/plugins/plugins.tpl.html',
            controller: 'pluginsController',
            controllerAs: 'pC'
        })
        .state('dashboard.account', {
            name: 'account',
            url: '/account',
            templateUrl: '/app/views/account/account.tpl.html',
            controller: 'accountController',
            controllerAs: 'aC'
        })
});

// Prevents user from views that they shouldn't be able to see
// minecraftControlPanel.run([
//     '$rootScope',
//     '$state',
//     '$window',
//     'Auth',
//     function($rootScope, $state, $window, Auth) {
//         $rootScope.$on('$stateChangeStart', function(e, to) {
//             validToken = Auth.validToken();
//             if (to.name == "login") {
//                 if (validToken == true) {
//                     e.preventDefault();
//                     $state.go('dashboard');
//                     $state.reload();
//                 } else {
//                     return;
//                 }
//             } else {
//                 if (to.data.requireAuth && !validToken) {
//                     e.preventDefault();
//                     $state.go('login');
//                 }
//             }
//         });
//     },
// ]);
