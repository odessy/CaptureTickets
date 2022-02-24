	// create the module and name it myApp
	var myApp = angular.module('myApp', ['ngRoute', 'ngSanitize']);

	// configure our routes
	myApp.config(function($routeProvider) {
		$routeProvider

			// route for the home page
			.when('/', {
				templateUrl : 'pages/home.html',
				controller  : 'mainController'
			})

			// route for the about page
			.when('/about', {
				templateUrl : 'pages/about.html',
				controller  : 'aboutController'
			})
	});

	myApp.filter('unescape', function(){
		return function(str){
			//return $sce.parseAsHtml(str);
			return str.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
		}
	});
	
	myApp.filter('fixUrl', function() {

	  // In the return function, replace the following string in url.
	  return function(url) {
		return url
		//.replace("/api/tickets","/groove_client/tickets")
		.replace("/reply", "")
		.replace("/api/v0/conversations/", "/conversation/")
		.replace("/api/v2/tickets", "/tickets")
	  }

	});
	
	myApp.filter('reverse', function() {
	  return function(items) {
		if(items) return items.slice().reverse();
	  };
	});
	
	// create the controller and inject Angular's $scope
	myApp.controller('mainController', function($scope, $http, $interval) {
		
		$scope.$http = $http;
		
		$scope.callAtInterval = function() {
			$scope.$http.get("http://localhost/tickets/readTicket.php").then(function(response) {
				$scope.data = response.data;
			});
		}
		
		$scope.callAtInterval();

		$scope.intervalCall = $interval($scope.callAtInterval, 180000);
		
		$scope.$on('$destroy', function() {
			// Make sure that the interval is destroyed too
			$interval.cancel($scope.intervalCall);
		});
	});

	myApp.controller('aboutController', function($scope) {
		$scope.message = 'Allows you to display your tickets captured with browser extension';
	});