angular.module("myapp", ['ngSanitize'])
       .controller("MoxieMovieController", function ($scope, $http) {

           $http.get('movies.json').then(
              // success handler
              function (response) {
                 $scope.data = response.data.data;
              },
              // error handler
              function (response) {
                 $scope.data = response.data.data;
              });
       });