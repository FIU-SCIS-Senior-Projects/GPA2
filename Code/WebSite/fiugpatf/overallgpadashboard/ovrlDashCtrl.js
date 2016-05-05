var myApp = angular.module('myApp', [ 'ngMaterial', 'md.data.table', 'chart.js']);

myApp.controller('ovrlDashCtrl', ['$scope', '$mdDialog', 'gpaDashService', function($scope, $mdDialog, gpaDashService){
    $scope.displayNeeded = [];
    $scope.displayTaken = [];
    $scope.selectNeeded = [];
    $scope.selectTaken = [];
    $scope.countTaken = 0;
    $scope.countNeeded = 0;
    $scope.currentProgram = [];
    $scope.programs = [] ;
    $scope.targetProgram = null;
    $scope.searchText = null;
    $scope.gpa = null;
    $scope.targetGPA = null;
    $scope.bucketTaken = null;
    $scope.bucketNeeded = null;
    $scope.limitOptions = [5, 10, 15];
    var takenTable;
    var neededTable;
    var first = 0;

    $scope.queryTaken = {
        order: 'name',
        limit: 4,
        page: 1
    };

    $scope.queryNeeded = {
        order: 'name',
        limit: 4,
        page: 1
    };

    gpaDashService.initial().then(function() {
        takenTable = document.getElementById('taken');
        //neededTable = document.getElementById('needed');
        //$scope.displayNeeded = gpaDashService.neededPop();
        $scope.displayTaken = gpaDashService.takenPop();

        $scope.countTaken = $scope.displayTaken.length;
        //$scope.countNeeded = $scope.displayNeeded.length;
    });

    gpaDashService.getMajorBucketsNeeded().then(function() {
        neededTable = document.getElementById('needed');
        $scope.displayNeeded = gpaDashService.neededPop();
        $scope.countNeeded = $scope.displayNeeded.length;
    });

    gpaDashService.setPrograms().then(function() {
        $scope.programs = gpaDashService.getPrograms();
        $scope.currentProgram = gpaDashService.getCurrentProgram();
        $scope.targetGPA = gpaDashService.getTargetGPA();
    });

    gpaDashService.setGPA().then(function() {
        $scope.gpa = gpaDashService.getGPA();
    });

    $scope.getChildren = function (bucket) {
        if ($scope.bucketTaken != null)
            gpaDashService.takenBucketPush($scope.bucketTaken);

        $scope.bucketTaken = bucket.name;
        gpaDashService.takenPush($scope.displayTaken);

        gpaDashService.findChildBuckets(bucket).then(function(response){
            if (response.success == true) {
                gpaDashService.getChildBucketsTaken(bucket).then(function(){
                    $scope.displayTaken = gpaDashService.takenPop();
                    $scope.countTaken = $scope.displayTaken.length;
                });

                if (takenTable.id =='taken')
                {
                    takenTable.style.display="none";
                    takenTable = document.getElementById('takenChild');
                    takenTable.style.display="block";
                }
            }
            else {
                gpaDashService.getCoursesTaken(bucket).then(function(){
                    $scope.displayTaken = gpaDashService.takenPop();
                    $scope.countTaken = $scope.displayTaken.length;
                });
                takenTable.style.display="none";
                takenTable = document.getElementById('takenCourses');
                takenTable.style.display="block";
            }

        });
    };

    $scope.getChildrenNeeded = function (bucket) {
        if ($scope.bucketNeeded != null)
            gpaDashService.neededBucketPush($scope.bucketNeeded);

        $scope.bucketNeeded = bucket.name;
        gpaDashService.neededPush($scope.displayNeeded);

        gpaDashService.findChildBuckets(bucket).then(function(response){
            if (response.success == true) {
                gpaDashService.getChildBucketsNeeded(bucket).then(function(){
                    $scope.displayNeeded = gpaDashService.neededPop();
                    $scope.countNeeded = $scope.displayNeeded.length;
                });

                if (neededTable.id =='needed')
                {
                    neededTable.style.display="none";
                    neededTable = document.getElementById('neededChild');
                    neededTable.style.display="block";
                }
            }
            else {
                gpaDashService.getCoursesNeeded(bucket).then(function(){
                    $scope.displayNeeded = gpaDashService.neededPop();
                    $scope.countNeeded = $scope.displayNeeded.length;
                });
                neededTable.style.display="none";
                neededTable = document.getElementById('neededCourses');
                neededTable.style.display="block";
            }

        });
    };

    $scope.takenBack = function () {
        if (gpaDashService.takenBucketLength() == 0)
            $scope.bucketTaken = null;
        else
            $scope.bucketTaken = gpaDashService.takenBucketPop();

        $scope.displayTaken = gpaDashService.takenPop();
        $scope.countTaken = $scope.displayTaken.length;

        if (gpaDashService.takenLength() == 0)
        {
            takenTable.style.display="none";
            takenTable = document.getElementById("taken");
            takenTable.style.display="block";
        }
        else if (takenTable.id =='takenCourses')
        {
            takenTable.style.display="none";
            takenTable = document.getElementById('takenChild');
            takenTable.style.display="block";
        }
    };

    $scope.neededBack = function () {
        if (gpaDashService.neededBucketLength() == 0)
            $scope.bucketNeeded = null;
        else
            $scope.bucketNeeded= gpaDashService.neededBucketPop();

        $scope.displayNeeded = gpaDashService.neededPop();
        $scope.countNeeded = $scope.displayNeeded.length;

        if (gpaDashService.neededLength() == 0)
        {
            neededTable.style.display="none";
            neededTable = document.getElementById("needed");
            neededTable.style.display="block";
        }
        else if (neededTable.id =='neededCourses')
        {
            neededTable.style.display="none";
            neededTable = document.getElementById('neededChild');
            neededTable.style.display="block";
        }
    };

    $scope.change = function(course) {
        for (i=0; i<$scope.countNeeded; i++)
        {
            var obj = $scope.displayNeeded[i];
            if (obj.id == course.id) {
                if ((i+1) % 2 == 1) {
                    course.color = {"background-color": '#ffffff'};
                }
                else {
                    course.color = {"background-color": 'rgba(166, 200, 254, 1)'};
                }
                break;
            }
        }
        gpaDashService.setCourseSelected(course);
        $mdDialog.show({
            controller: dialogCtrl,
            templateUrl: 'changeDialog.html',
            parent: angular.element(document.body),
            clickOutsideToClose:true,
            fullscreen: true
        });
    };

    $scope.showSlider = function() {
        if ($scope.targetProgram == null)
            return true;
        else
            return false;
    };

    $scope.changeTargetGPA = function(targetProgram) {
        if ($scope.targetProgram == null) {
            $scope.targetGPA = 2;
        }
        else {
            $scope.targetGPA = $scope.targetProgram.gpa;
        }

    };

    $scope.$watch('targetGPA', function() {
        if (first > 1 && $scope.targetGPA != null)
            gpaDashService.saveTargetGPA($scope.targetGPA);
        else {
            first ++;
        }
    });

    $scope.expandTaken = function() {
        gpaDashService.takenPush($scope.displayTaken);

        takenTable.style.display="none";
        takenTable = document.getElementById('allTakenCourses');
        takenTable.style.display="block";

        gpaDashService.setAllCoursesTaken().then(function(){
            $scope.displayTaken = gpaDashService.getAllCoursesTaken();
            $scope.countTaken = $scope.displayTaken.length;
        });

    };

    $scope.collapseTaken = function() {
        takenTable.style.display="none";
        takenTable = document.getElementById('taken');
        takenTable.style.display="block";

        $scope.displayTaken = gpaDashService.takenPop();
        $scope.countTaken = $scope.displayTaken.length;
    };

    $scope.expandNeeded = function() {
        gpaDashService.neededPush($scope.displayNeeded);

        neededTable.style.display="none";
        neededTable = document.getElementById('allNeededCourses');
        neededTable.style.display="block";

        gpaDashService.setAllCoursesNeeded().then(function(){
            $scope.displayNeeded = gpaDashService.getAllCoursesNeeded();
            $scope.countNeeded = $scope.displayNeeded.length;
        });
    };

    $scope.collapseNeeded = function() {
        neededTable.style.display="none";
        neededTable = document.getElementById('needed');
        neededTable.style.display="block";

        $scope.displayNeeded = gpaDashService.neededPop();
        $scope.countNeeded = $scope.displayNeeded.length;
    };

    $scope.generateForecast = function() {
        var imported = false;
        var allSet = false;
        var gpaSet = false;

/*        gpaDashService.getGoalGPA().then(function(response){
            if (response != null)
                gpaSet = true;
            else {
                alert = $mdDialog.alert()
                    .textContent("GPA goal must be set.")
                    .ok('Close');
                $mdDialog
                    .show(alert)
                    .finally(function () {
                        alert = undefined;
                    });
            }
        });

        gpaDashService.checkImport().then(function(response){
            if (response === 'No grades')
                alert(response);
            else {
                alert = $mdDialog.alert()
                    .textContent("No classes are available to generate GPA forecast.\n Please speak to an adviser for further assistance.")
                    .ok('Close');
                $mdDialog
                    .show(alert)
                    .finally(function () {
                        alert = undefined;
                    });
            }

        });

        gpaDashService.checkWeightAndRelevance().then(function(response){
            if (response == true)
                allSet = true;
            else {
                alert = $mdDialog.alert()
                    .textContent("Select the weight and relevance for ALL\nin-progress and remaining courses.")
                    .ok('Close');
                $mdDialog
                    .show(alert)
                    .finally(function () {
                        alert = undefined;
                    });
            }
        });

        if(imported && allSet && gpaSet)*/
            window.open('GPAForecastReport.html', 'GPA Forecast Report');

    };

}]);

myApp.controller("graphController", ['$scope', '$http', function ($scope, $http) {

    var data = {
        'action' : "getGraphData"
    };

    $http.post('OvrlDashRouter.php',data).success( function(response) {
        var series = ['GPA AVERAGE AT FIU'];

        $scope.labels = response[0];
        $scope.data = [response[1]];
        $scope.series = series;

        $scope.onClick = function (points, evt) {
            console.log(points, evt);
        };
    });
}]);

myApp.service('gpaDashService', ['$q', '$http', function ($q, $http) {
    var service = {};
    var router = 'OvrlDashRouter.php';

    var neededStack = [];
    var takenStack = [];
    var neededBucketStack = [];
    var takenBucketStack = [];
    var courseSelected;
    var currentProgram;
    var programs = [];
    var allCoursesTaken = [];
    var allCoursesNeeded = [];
    var gpa ;
    var targetGPA;

    service.initial = function() {
        var deffered = $q.defer();

        var data = {'action': 'getMajorBuckets'};

        $http.post(router, data).success(function (response)
        {
            takenStack.push(response);
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.getMajorBucketsNeeded = function() {
        var deffered = $q.defer();

        var data = {'action': 'getMajorBucketsNeeded'};

        $http.post(router, data).success(function (response)
        {
            neededStack.push(response);
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.setCourseSelected =function(s) {
        courseSelected = s;
    };

    service.getCourseSelected =function() {
        return courseSelected;
    };

    service.neededPop = function() {
        return neededStack.pop();
    };

    service.neededPush = function(s) {
        neededStack.push(s);
    };

    service.neededBucketPop = function() {
        return neededBucketStack.pop();
    };

    service.neededBucketPush = function(s) {
        neededBucketStack.push(s);
    };

    service.takenPop = function() {
        return takenStack.pop();
    };

    service.takenPush = function(s) {
        takenStack.push(s);
    };

    service.takenBucketPop = function() {
        return takenBucketStack.pop();
    };

    service.takenBucketPush = function(s) {
        takenBucketStack.push(s);
    };

    service.findChildBuckets = function(bucket) {
        var deffered = $q.defer();

        var data = {
            'action': 'findChildBuckets',
            'bucket': bucket};

        $http.post(router, data).success(function (response)
        {
            deffered.resolve(response);
        });

        return deffered.promise;
    };

    service.getCoursesTaken = function(bucket) {
        var deffered = $q.defer();

        var data = {
            'action': 'getMajorBucketsCourse',
            'bucket': bucket};

        $http.post(router, data).success(function (response)
        {
            takenStack.push(response);
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.getCoursesNeeded = function(bucket) {
        var deffered = $q.defer();

        var data = {
            'action': 'getMajorBucketsCourseNeeded',
            'bucket': bucket};

        $http.post(router, data).success(function (response)
        {
            neededStack.push(response);
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.getChildBucketsTaken = function(bucket) {
        var deffered = $q.defer();

        var data = {
            'action': 'getMajorBucketsChildBuckets',
            'bucket': bucket};

        $http.post(router, data).success(function (response)
        {
            takenStack.push(response);
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.getChildBucketsNeeded = function(bucket) {
        var deffered = $q.defer();

        var data = {
            'action': 'getMajorBucketsChildBuckets',
            'bucket': bucket};

        $http.post(router, data).success(function (response)
        {
            neededStack.push(response);
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.takenLength = function() {
        return takenStack.length;
    };

    service.neededLength = function() {
        return neededStack.length;
    };

    service.takenBucketLength = function() {
        return takenBucketStack.length;
    };

    service.neededBucketLength = function() {
        return neededBucketStack.length;
    };

    service.saveData = function() {
        var deffered = $q.defer();

        var data = {
            'action': 'modWeight',
            'course': courseSelected};

        $http.post(router, data).success(function (response)
        {
            takenStack.push(response);
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.setPrograms = function() {
        var deffered = $q.defer();

        var data = {
            'action': 'getProgramInfo'};

        $http.post(router, data).success(function (response)
        {
            currentProgram = response[0];
            targetGPA = response[1];
            programs = response[2];
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.getPrograms = function() {
        return programs;
    };

    service.getCurrentProgram = function() {
        return currentProgram;
    };

    service.getTargetGPA = function() {
        return targetGPA;
    };

    service.setGPA = function() {
        var deffered = $q.defer();

        var data = {
            'action': 'getGPA'};

        $http.post(router, data).success(function (response)
        {
            gpa = response[0];
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.getGPA = function() {
        return gpa;
    };

    service.saveTargetGPA = function (gpa) {
        var deffered = $q.defer();

        var data = {
            'action': 'saveTargetGPA',
            'gpa': gpa};

        $http.post(router, data).success(function (response)
        {
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.setAllCoursesTaken = function() {
        var deffered = $q.defer();

        var data = {
            'action': 'getAllCoursesTaken'};

        $http.post(router, data).success(function (response)
        {
            allCoursesTaken = response;
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.getAllCoursesTaken = function() {
        return allCoursesTaken;
    };

    service.setAllCoursesNeeded = function() {
        var deffered = $q.defer();

        var data = {
            'action': 'getAllCoursesNeeded'};

        $http.post(router, data).success(function (response)
        {
            allCoursesNeeded = response;
            deffered.resolve();
        });

        return deffered.promise;
    };

    service.getAllCoursesNeeded = function() {
        return allCoursesNeeded;
    };

    service.checkImport = function() {
        var deffered = $q.defer();

        var data = {'action': 'takenAndRemaining'};

        $http.post(router, data).success(function (response)
        {
            deffered.resolve(response);
        });

        return deffered.promise;
    };

    service.checkWeightAndRelevance = function() {
        var deffered = $q.defer();
        var allSelected = true;
        var data = {'action': 'checkWeightAndRelevance'};

            $http.post(router, data).success(function (data)
            {
                for (var x = 0; x < data.length; x++) {
                    if(data[x][1] == 0) { //check if weight or relevance are null
                        allSelected = false;
                        break;
                    }
                }
                deffered.resolve(allSelected);
            });

            return deffered.promise;

    };

    service.getGoalGPA = function() {
        var deffered = $q.defer();

        var data = {'action': 'GPAGoal'};

        $http.post(router, data).success(function (response)
        {
            deffered.resolve(response[0][0]);
        });

        return deffered.promise;
    };

    return service;

}]);

function dialogCtrl($scope, $mdDialog, gpaDashService) {
    $scope.hide = function () {
        $mdDialog.hide();
    };
    $scope.cancel = function () {
        $mdDialog.cancel();
    };
    $scope.save = function () {
        $scope.selected.weight = $scope.weight;
        $scope.selected.relevance = $scope.relevance;
        gpaDashService.saveData().then(function(){

        });
        $mdDialog.hide();
    };
    $scope.selected = gpaDashService.getCourseSelected();

    $scope.weight = 1;
    $scope.relevance = 0;

}
