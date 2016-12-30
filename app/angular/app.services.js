//Login Service
minecraftControlPanel.service('loginService', [
    '$http',
    function($http) {
        this.parseLogin = function(data) {
            return $http({
                method: 'POST',
                url: CONFIG.API_URL + "auth/login",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                transformRequest: function(obj) {
                    var str = [];
                    for (var p in obj)
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    return str.join("&");
                },
                data: data
            })
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
                return angular.fromJson(atob(token_parts[1]))._doc;
            }
        }
    }
]);
