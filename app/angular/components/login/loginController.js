minecraftControlPanel.controller('loginController', [
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
                    Auth.setToken(res.data.jwt);
                    that.errorText = 'Logging you in.';

                    $timeout(function() {
                        $state.go('dashboard');
                        //decode token
                        var token_parts = res.data.jwt.split('.');
                        var output = angular.fromJson(atob(token_parts[1]));
                        $rootScope.$emit('userLogin', output);
                    }, 1500);
                } else {
                    that.errorText = res.data.message;
                }
            }, function errorCallback() {
                that.error = true;
                that.errorText = 'Please check your details and try again.';
            });
        };
    }
]);
