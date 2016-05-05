/**
 * Created by Lizette Mendoza on 4/6/16.
 */


angular.module('firstApplication', ['ngMaterial', 'md.data.table', 'chart.js'])

    // Optional configuration
    .config(['ChartJsProvider', function (ChartJsProvider) {
        // Configure all charts
        ChartJsProvider.setOptions({
            /*colours: ['#000080', '#FFD700', '#333333', '#0f6b2e', '#ff3300', '#7d00b3'],*/
            responsive: false
        });
        // Configure all line charts
        ChartJsProvider.setOptions('Line', {
            datasetFill: false
        });
    }])

    .service('dashboardService', ['$q', '$http', function ($q, $http) {
        var service = {};

        var router = 'semesterDashboardRouter.php';
        var settings = [
            {
                name: 'GPAGoal'
            },
            {
                name: 'takenAndRemaining'
            }
        ];

        service.initial = function () {
            var deffered = $q.defer();
            var actions = [
                {
                    'action': "GPAGoal"
                },
                {
                    'action': "takenAndRemaining"
                }
            ];

            $http.post(router, actions[0]).success(function (response) {
                settings[0].list = response;
            });
            $http.post(router, actions[1]).success(function (response) {
                settings[1].list = response;
                deffered.resolve();
            });

            return deffered.promise;
        };

        service.getSettings = function() {
            return settings;
        };

        return service;
    }])

    .controller("LineCtrl", ['$scope', '$http', function ($scope, $http) {

        var data = {
            'action' : "getGraphData"
        };

        $http.post('semesterDashboardRouter.php',data).success( function(response) {

            $scope.labels = response[0];
            $scope.data = response[1];

            $scope.onClick = function (points, evt) {
                console.log(points, evt);
            };
        });

        data = {
            'action' : "courseLegend"
        };
        $http.post('semesterDashboardRouter.php',data).success( function(response) {
            $scope.series = response;
        });


    }])

    .controller('checkForecastController', ['$scope', 'dashboardService', checkForecastReport])

    .controller('dashboardTableController', ['$mdEditDialog', '$q', '$scope', '$http', '$mdDialog', function ($mdEditDialog, $q, $scope, $http, $mdDialog) {

        $scope.selected = [];
        $scope.limitOptions = [5, 10, 15];

        $scope.query = {
            order: 'name',
            limit: 5,
            page: 1
        };

        var data = {
            'action' : "currentAssessments"
        };

        $http.post('semesterDashboardRouter.php',data).success( function(response) {
            $scope.return = response;

            $scope.courses = { //data tables
                "count": 4,
                "data": response
            }
        });

        $scope.options = {
            rowSelection: true,
            multiSelect: true,
            autoSelect: true,
            largeEditDialog: false,
            boundaryLinks: false,
            pageSelect: true
        };

        $scope.toggleLimitOptions = function () {
            $scope.limitOptions = $scope.limitOptions ? undefined : [5, 10, 15];
        };

        $scope.logItem = function (item) {
            console.log(item.name, 'was selected');
            window.location.replace('assessmentBreakdown.html?id='+item.id, 'Assessment Breakdown');
        };

        $scope.logOrder = function (order) {
            console.log('order: ', order);
        };

        $scope.logPagination = function (page, limit) {
            console.log('page: ', page);
            console.log('limit: ', limit);
        };
    }]);

    function checkForecastReport($scope, dashboardService) {

        $scope.checkGenerateForecast = function() {

            var self = this;
            self.settings = [];
            $scope.list = [];
            var classesImported = true;
            var gpaSelected = true;

            dashboardService.initial().then(function(){
                self.settings = dashboardService.getSettings();
                console.log(self.settings[0].list);
                console.log(self.settings[1].list);

                var data = self.settings[0].list;
                var GPAGoal = data[0][0];

                data = self.settings[1].list;
                var creditsLeft = parseInt(data[0][0]);

                if(creditsLeft === null) { //check if values are null
                    classesImported = false;
                }
                else if (GPAGoal === null){
                    gpaSelected = false;
                }
                else {
                    window.open('../../sem_dashboard/semesterForecastReport.html', 'Semester GPA Forecast Report');
                }

            });
        };
    }


