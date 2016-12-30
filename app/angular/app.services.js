//Login Service
minecraftControlPanel.service('loginService', [
    '$http',
    function($http) {
        this.parseLogin = function(data) {
            return $http.post("/auth/login", data);
        }
    }
]);

//JWT Auth Factory
minecraftControlPanel.factory('Auth', [
    '$window',
    function($window) {
        var tokenKey = 'user-token';
        var storage = $window.localStorage;
        var cachedToken;
        return {
            setToken: setToken,
            getToken: getToken,
            validToken: validToken,
            clearToken: clearToken,
            decodeJWT: decodeJWT()
        };

        function setToken(token) {
            cachedToken = token;
            storage.setItem(tokenKey, token);
        }

        function getToken() {
            if (!cachedToken) {
                cachedToken = storage.getItem(tokenKey);
            }
            return cachedToken;
        }

        function validToken() {
            return !!getToken();
        }

        function clearToken() {
            cachedToken = null;
            storage.removeItem(tokenKey);
        }

        function decodeJWT() {
            if (validToken()) {
                var token_parts = getToken().split('.');
                return angular.fromJson(atob(token_parts[1]));
            }
        }
    }
]);

minecraftControlPanel.service('dashboardService', ['$http', function($http)
{
    var that = this;
}]);
