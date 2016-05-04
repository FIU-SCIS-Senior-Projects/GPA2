/**
 * Created by sproject on 4/15/16.
 */
var course;

angular.module('breakdownApp', ['ngMaterial', 'md.data.table', 'chart.js', 'ngMessages', 'ngSanitize'])

    .run(function() {
        course = getUrlVars()["id"];
    })

    // Optional configuration
    .config(['ChartJsProvider', function (ChartJsProvider) {
        // Configure all charts
        ChartJsProvider.setOptions({
            colours: ['#000080', '#FFD700', '#333333', '#0f6b2e', '#ff3300', '#7d00b3'],
            responsive: false
        });
        // Configure all line charts
        ChartJsProvider.setOptions('Line', {
            datasetFill: false
        });
    }])

    .service('breakdownService', ['$q', '$http', function ($q, $http) {
        var service = {};
        var router = 'semesterDashboardRouter.php';
        var settings = [
            {
                name: 'Tabs'
            },
            {
                name: 'GetAllAssessments'
            }
        ];
        var grades = [];
        var tabSelected = 0;

        service.initial = function () {
            var deffered = $q.defer();
            var actions = [
                {
                    'action': "tabs",
                    'course': course
                },
                {
                    'action': "getAllAssessments",
                    'course': course
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

        service.saveData = function(assessment, percentage) {
            var deffered = $q.defer();

            var data = {
                'action': 'add',
                'assessment': assessment,
                'percentage': percentage,
                'course': course
            };

            $http.post(router, data).success(function (response)
            {
                console.log('Add assessment: success');
                deffered.resolve();
            });

            return deffered.promise;
        };

        service.removeData = function(tab) {
            var deffered = $q.defer();

            var data = {
                'action': 'removeBucket',
                'assessment': settings[0].list[tab-1],
                'course': course
            };

            $http.post(router, data).success(function (response)
            {
                console.log('Add assessment: success');
                deffered.resolve();
            });

            return deffered.promise;
        };

        service.addGrade = function(grade) {
            var deffered = $q.defer();

            var data = {
                'action': 'addGrade',
                'assessment': settings[0].list[tabSelected-1],
                'grade': grade,
                'course': course
            };

            $http.post(router, data).success(function (response)
            {
                console.log('Add Grade: success');
                deffered.resolve();
            });

            return deffered.promise;
        };

        service.removeGrade = function (grade) {
            var deffered = $q.defer();
            console.log(grade);
            var data = {
                'action': 'removeGrade',
                'assessment': settings[0].list[tabSelected-1],
                'grade': grade,
                'course': course
            };

            $http.post(router, data).success(function (response)
            {
                console.log('Remove Grade: success');
                deffered.resolve();
            });

            return deffered.promise;
        };

        service.modifyGrade = function (assessment, oldGrade, newGrade) {
            var deffered = $q.defer();

            var data = {
                'action': 'modifyGrade',
                'assessment': assessment,
                'grade': oldGrade,
                'newGrade': newGrade,
                'course': course
            };

            $http.post(router, data).success(function (response)
            {
                console.log('Modify Grade: success');
                deffered.resolve();
            });

            return deffered.promise;
        };

        service.setGrades = function (list) {
            grades = list;
        };

        service.getGrades = function () {
            var x = grades[tabSelected - 1];

            for(var i = 0; i < x.length; i++)
            {
                var grade = x[i];
                if (grade.hasOwnProperty('id0')) {
                    grade.name = grade.id0;
                    grade.value = grade.grade0;
                }
                else if (grade.hasOwnProperty('id1')) {
                    grade.name = grade.id1;
                    grade.value = grade.grade1;
                }
                else if (grade.hasOwnProperty('id2')) {
                    grade.name = grade.id2;
                    grade.value = grade.grade2;
                }
                else if (grade.hasOwnProperty('id3')) {
                    grade.name = grade.id3;
                    grade.value = grade.grade3;
                }
                else if (grade.hasOwnProperty('id4')) {
                    grade.name = grade.id4;
                    grade.value = grade.grade4;
                }
                else if (grade.hasOwnProperty('id5')) {
                    grade.name = grade.id5;
                    grade.value = grade.grade5;
                }
                else if (grade.hasOwnProperty('id6')) {
                    grade.name = grade.id6;
                    grade.value = grade.grade6;
                }
            }
            return x;
        };

        service.setTabSelected = function (i) {
            tabSelected = i;
        };

        return service;
    }])

    .controller("LineCtrl", ['$scope', '$http', function ($scope, $http) {

        var data1 = {};
        data1["action"] = "plotPoints";
        data1["list"] = course;

        $http.post('semesterDashboardRouter.php',data1).success( function(response) {
            $scope.labels = response[0];
            $scope.series = [course];
            $scope.data = [
                response[1]
            ];

            $scope.onClick = function (points, evt) {
                console.log(points, evt);
            };
        });
    }])

    .controller('dashboardTableController', ['$mdEditDialog', '$q', '$scope', '$http', function ($mdEditDialog, $q, $scope, $http) {

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

        $scope.toggleLimitOptions = function () {
            $scope.limitOptions = $scope.limitOptions ? undefined : [5, 10, 15];
        };

        $scope.logItem = function (item) {
            console.log(item.name, 'was selected');
        };

        $scope.logOrder = function (order) {
            console.log('order: ', order);
        };

        $scope.logPagination = function (page, limit) {
            console.log('page: ', page);
            console.log('limit: ', limit);
        };

    }])

    .controller('AppCtrl', ['$scope', '$log', '$http', 'breakdownService', '$mdDialog', '$mdMedia', function  ($scope, $log, $http, breakdownService, $mdDialog, $mdMedia) {

        var self = this;
        self.settings = [];
        $scope.list = [];
        var router = 'semesterDashboardRouter.php';
        var returnedTabs = [];
        var returnedAvg = [];
        var returnedGrades0 = [];
        var returnedGrades1 = [];
        var returnedGrades2 = [];
        var returnedGrades3 = [];
        var returnedGrades4 = [];
        var returnedGrades5 = [];
        var returnedGrades6 = [];
        var next = 0;
        var data1 = {};
        var tabs = [];


        breakdownService.initial().then(function(){
            self.settings = breakdownService.getSettings();
            returnedTabs = self.settings[0].list;
            returnedAvg = self.settings[1].list;

            $scope.mainTitle = 'Assessment Management';

            data1["action"] = "getGrades";
            data1["course"] = course;

            if(returnedTabs[0] != null) {
                data1["assessment"] = returnedTabs[0];
                next++;

                $scope.title0 = returnedTabs[0];

                $http.post(router, data1).success(function (response) {

                    for(var i = 0; i < response.length; i++) {
                        returnedGrades0.push({
                            "id0": response[i][0],
                            "grade0": response[i][1]
                        });
                    }

                    $scope.grades0 = { //data tables
                        "data": returnedGrades0
                    };

                    console.log(returnedGrades0);
                });
            }
        }).then(function() {

            if(returnedTabs[1] != null) {
                data1["assessment"] = returnedTabs[1];
                next++;

                $scope.title1 = returnedTabs[1];

                $http.post(router, data1).success(function (response) {
                    for(var i = 0; i < response.length; i++) {
                        returnedGrades1.push({
                            "id1": response[i][0],
                            "grade1": response[i][1]
                        });
                    }

                    $scope.grades1 = { //data tables
                        "data": returnedGrades1
                    };

                    console.log(returnedGrades1);
                });


            }

        }).then(function() {

            if(returnedTabs[2] != null) {
                data1["assessment"] = returnedTabs[2];
                next++;

                $scope.title2 = returnedTabs[2];

                $http.post(router, data1).success(function (response) {
                    for(var i = 0; i < response.length; i++) {
                        returnedGrades2.push({
                            "id2": response[i][0],
                            "grade2": response[i][1]
                        });
                    }

                    $scope.grades2 = { //data tables
                        "data": returnedGrades2
                    };
                });
            }

        }).then(function() {

            if(returnedTabs[3] != null) {
                data1["assessment"] = returnedTabs[3];
                next++;

                $scope.title3 = returnedTabs[3];

                $http.post(router, data1).success(function (response) {
                    for(var i = 0; i < response.length; i++) {
                        returnedGrades3.push({
                            "id3": response[i][0],
                            "grade3": response[i][1]
                        });
                    }

                    $scope.grades3 = { //data tables
                        "data": returnedGrades3
                    };
                });
            }

        }).then(function() {

            if(returnedTabs[4] != null) {
                data1["assessment"] = returnedTabs[4];
                next++;

                $scope.title4 = returnedTabs[4];

                $http.post(router, data1).success(function (response) {
                    for(var i = 0; i < response.length; i++) {
                        returnedGrades4.push({
                            "id4": response[i][0],
                            "grade4": response[i][1]
                        });
                    }

                    $scope.grades4 = { //data tables
                        "data": returnedGrades4
                    };
                });
            }

        }).then(function() {

            if(returnedTabs[5] != null) {
                data1["assessment"] = returnedTabs[5];
                next++;

                $scope.title5 = returnedTabs[5];

                $http.post(router, data1).success(function (response) {
                    for(var i = 0; i < response.length; i++) {
                        returnedGrades5.push({
                            "id5": response[i][0],
                            "grade5": response[i][1]
                        });
                    }

                    $scope.grades5 = { //data tables
                        "data": returnedGrades5
                    };
                });
            }

        }).then(function() {

            if(returnedTabs[6] != null) {
                data1["assessment"] = returnedTabs[6];
                next++;

                $scope.title6 = returnedTabs[6];

                $http.post(router, data1).success(function (response) {
                    for(var i = 0; i < response.length; i++) {
                        returnedGrades6.push({
                            "id6": response[i][0],
                            "grade6": response[i][1]
                        });
                    }

                    $scope.grades6 = { //data tables
                        "data": returnedGrades6
                    };
                });
            }

        }).then(function() {

            tabs = [{title1: 'Assessment Management'}];

            $scope.courses = { //data tables
                "data": returnedAvg
            };
            console.log(returnedAvg);

            var selected = null,
                previous = null;
            $scope.tabs = tabs;
            $scope.selectedIndex = 0;

            console.log($scope.selectedIndex);

            $scope.$watch('selectedIndex', function(current, old){
                /*previous = selected;
                selected = tabs[current];
                if ( old + 1 && (old != current)) $log.debug('Goodbye ' + previous + '!');
                if ( current + 1 )                $log.debug('Hello ' + selected + '!');*/
            });

            //POP UP - ADD BUTTON
            $scope.showAdvanced = function(ev) {
                var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
                $mdDialog.show({
                        controller: DialogController,
                        templateUrl: 'addAssessment.html',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose:true,
                        fullscreen: useFullScreen
                    })
                    .then(function(answer) {
                        console.log('You said the information was "' + answer + '".');
                    }, function() {
                        console.log('You cancelled the dialog.');
                    });
                $scope.$watch(function() {
                    return $mdMedia('xs') || $mdMedia('sm');
                }, function(wantsFullScreen) {
                    $scope.customFullscreen = (wantsFullScreen === true);
                });
            };

            //POP UP - REMOVE BUTTON
            $scope.showAdvanced2 = function(ev) {
                /*var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
                $mdDialog.show({
                        controller: DialogController2,
                        templateUrl: 'removeAssessment.html',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose:true,
                        fullscreen: useFullScreen
                    })
                    .then(function(answer) {
                        console.log('You said the information was "' + answer + '".');
                    }, function() {
                        console.log('You cancelled the dialog.');
                    });
                $scope.$watch(function() {
                    return $mdMedia('xs') || $mdMedia('sm');
                }, function(wantsFullScreen) {
                    $scope.customFullscreen = (wantsFullScreen === true);
                });*/

                breakdownService.removeData($scope.selectedIndex).then(function(){
                    location.reload();
                });
            };

            //POP UP - ADD GRADE BUTTON
            $scope.showAdvanced3 = function(ev) {
                var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
                $mdDialog.show({
                        controller: DialogController3,
                        templateUrl: 'addGrade.html',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose:true,
                        fullscreen: useFullScreen
                    })
                    .then(function(answer) {
                        console.log('You said the information was "' + answer + '".');
                    }, function() {
                        console.log('You cancelled the dialog.');
                    });
                $scope.$watch(function() {
                    return $mdMedia('xs') || $mdMedia('sm');
                }, function(wantsFullScreen) {
                    $scope.customFullscreen = (wantsFullScreen === true);
                });
                breakdownService.setTabSelected($scope.selectedIndex);
            };

            //POP UP - MODIFY GRADE BUTTON
            $scope.showAdvanced4 = function(ev) {
                var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
                $mdDialog.show({
                        controller: DialogController4,
                        templateUrl: 'modifyGrade.html',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose:true,
                        fullscreen: useFullScreen
                    })
                    .then(function(answer) {
                        console.log('You said the information was "' + answer + '".');
                    }, function() {
                        console.log('You cancelled the dialog.');
                    });
                $scope.$watch(function() {
                    return $mdMedia('xs') || $mdMedia('sm');
                }, function(wantsFullScreen) {
                    $scope.customFullscreen = (wantsFullScreen === true);
                });
            };

            //POP UP - REMOVE GRADE BUTTON
            $scope.showAdvanced5 = function(ev) {
                var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
                $mdDialog.show({
                        controller: DialogController5,
                        templateUrl: 'removeGrade.html',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose:true,
                        fullscreen: useFullScreen
                    })
                    .then(function(answer) {
                        console.log('You said the information was "' + answer + '".');
                    }, function() {
                        console.log('You cancelled the dialog.');
                    });
                $scope.$watch(function() {
                    return $mdMedia('xs') || $mdMedia('sm');
                }, function(wantsFullScreen) {
                    $scope.customFullscreen = (wantsFullScreen === true);
                });
                breakdownService.setTabSelected($scope.selectedIndex);
                var grades = [returnedGrades0, returnedGrades1, returnedGrades2, returnedGrades3, returnedGrades4, returnedGrades5, returnedGrades6];
                breakdownService.setGrades(grades);
            };

        });
    }]);

    function getUrlVars() {
        var map = {};
        window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
            map[key] = value;
        });
        return map;
    }

    function DialogController($scope, $mdDialog, breakdownService) {
        $scope.hide = function() {
            $mdDialog.hide();
        };
        $scope.cancel = function() {
            $mdDialog.cancel();
        };
        $scope.answer = function(answer, answer2) {

            breakdownService.saveData(answer,answer2);
            $mdDialog.hide();
            location.reload();
        };
    }

    function DialogController2($scope, $mdDialog, breakdownService) {
        $scope.hide = function() {
            $mdDialog.hide();
        };
        $scope.cancel = function() {
            $mdDialog.cancel();
        };
        $scope.answer = function(answer, answer2) {

            breakdownService.removeData(answer,answer2);

            $mdDialog.hide();
            location.reload();
        };
    }

    function DialogController3($scope, $mdDialog, breakdownService) {
        $scope.hide = function() {
            $mdDialog.hide();
        };
        $scope.cancel = function() {
            $mdDialog.cancel();
        };
        $scope.answer = function(answer) {

            breakdownService.addGrade(answer).then(function(){
                $mdDialog.hide();
                location.reload();
            });

        };
    }

    function DialogController4($scope, $mdDialog, breakdownService) {
        $scope.hide = function() {
            $mdDialog.hide();
        };
        $scope.cancel = function() {
            $mdDialog.cancel();
        };
        $scope.answer = function(answer, answer2, answer3) {


            breakdownService.modifyGrade(answer,answer2,answer3);

            $mdDialog.hide();
            /*location.reload();*/
        };
    }

    function DialogController5($scope, $mdDialog, breakdownService) {
        $scope.hide = function() {
            $mdDialog.hide();
        };
        $scope.cancel = function() {
            $mdDialog.cancel();
        };
        $scope.answer = function(answer) {

            breakdownService.removeGrade(answer).then(function(){
                $mdDialog.hide();
                location.reload();
            });


        };

        $scope.grades = breakdownService.getGrades();
        console.log($scope.grades[0]);
    }