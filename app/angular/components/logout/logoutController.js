minecraftControlPanel.controller('logoutController', ['Auth', '$state', function(Auth, $state)
{
    Auth.clearToken();
    $state.go('login');
}]);
