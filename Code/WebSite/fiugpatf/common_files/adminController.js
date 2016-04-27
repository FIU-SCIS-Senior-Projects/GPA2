var myApp = angular.module('myApp', [ 'ngMaterial', 'md.data.table']);

myApp.controller('myCtrl', ['$scope', 'fileUpload', '$mdDialog', 'adminService',
    function($scope, fileUpload, $mdDialog, adminService){

    var self = this;

    self.selected     = null;
    $scope.select = [];
    $scope.list = [];
    self.users        = [ ];
    self.settings = [];
    self.selectUser   = selectUser;
    self.courses = [];
    var displayed = -1;

    $scope.limitOptions = [5, 10, 15];
    $scope.query = {
        order: 'name',
        limit: 5,
        page: 1
    };

    adminService.initial().then(function(){
        self.settings = adminService.getSettings();
        selectUser(self.settings[0]);
    });

    function selectUser ( s ) {
        self.selected =  s;
        adminService.setSelectedSetting(s);
        if (displayed != -1)
        {
            displayed.style.display='none';
        }
        displayed = document.getElementById(self.selected.name);
        displayed.style.display="block";
    }

    $scope.toggleLimitOptions = function () {
        $scope.limitOptions = $scope.limitOptions ? undefined : [5, 10, 15];
    };

    $scope.addGrad = function(ev) {
        var newList = {"name":"","GPA": "","id":-1};
        adminService.setSelected(newList);
        $scope.logItem(newList);
    };

    $scope.displayCourses = function(student) {
        adminService.setStudent(student);
        adminService.setCourses().then(function(){
            self.courses = adminService.getCourses();
        });

        displayed.style.display='none';
        displayed = document.getElementById("Student Courses");
        displayed.style.display="block";
    };

    $scope.resetPass = function() {
        adminService.resetPassword();
    };

    $scope.logItem = function(item) {
        adminService.setSelected(item);
        $mdDialog.show({
            controller: dialogCtrl,
            templateUrl: 'dialog1.html',
            parent: angular.element(document.body),
            clickOutsideToClose:true,
            fullscreen: true
        });
        $scope.select = [];
    };

    $scope.uploadFile = function(){
        angular.element(document.querySelector('#inputfile')).val(null);
        var file = $scope.myFile;
        console.log('file is ' );
        console.dir(file);
        var uploadUrl = "router.php";
        fileUpload.uploadFileToUrl(file, uploadUrl);
    };

    $scope.addCourse = function () {
        var newList = {"name":"","courseID": "", "credits": "", "description": "", "id":-1};
        adminService.setSelected(newList);
        $scope.logItem(newList);
    };
}]);

function dialogCtrl($scope, $mdDialog, adminService) {
    $scope.hide = function () {
        $mdDialog.hide();
    };
    $scope.cancel = function () {
        $mdDialog.cancel();
    };
    $scope.answer = function (answer) {
        $mdDialog.hide(answer);
    };
    $scope.save = function () {
        adminService.saveData();
        showAlert();
        adminService.getGrad();
        adminService.updateCourse();
        $mdDialog.hide();
    };
    $scope.delete = function () {
        var confirm = $mdDialog.confirm()
            .title('Are you sure you want to delete?')
            .ok('Yes')
            .cancel('No');
        $mdDialog.show(confirm).then(function () {
            adminService.deleteData();
            $mdDialog.hide();
        }, function () {
            $mdDialog.hide();
        });
    };
    $scope.selected = adminService.getSelected();
    $scope.pnames = [];

    for (var name in $scope.selected) {
        var s = adminService.getSelectedSetting();

        if (name == "$$hashKey")
            continue;
        if (name == 'id')
            continue;
        if (s.name == "Update Student") {
            if (name == 'courseID' || name == 'courseName')
                continue;
        }


        $scope.pnames.push(name);
    }

    function showAlert() {
        alert = $mdDialog.alert()
            .textContent('Updated Successfully')
            .ok('Close');
        $mdDialog
            .show(alert)
            .finally(function () {
                alert = undefined;
            });
    }

    function closeAlert() {
        $mdDialog.hide(alert, "finished");
        alert = undefined;
    }
}

myApp.service('adminService', ['$q', '$http', function ($q, $http) {
    var service = {};

    var router = 'router.php';
    var selected;
    var selectedSetting;
    var selectedStudent;
    var courses = [];
    var settings =[
        {
            name: 'Update Grad Program',
        },
        {
            name: 'Update Curriculum',
        },
        {
            name: 'Update Buckets',
        },
        {
            name: 'Update Courses',
        },
        {
            name: 'Update Student',
        },
        {
            name: 'Import / Export'
        }
    ];

    service.initial = function() {
        var deffered = $q.defer();
        var actions = [
            {
                'action': "getGrad"
            },
            {
                'action': "getCurr"
            },
            {
                'action': "getBuckets"
            },
            {
                'action': "getCourses"
            },
            {
                'action': "getStudents"
            }
        ];


        $http.post(router, actions[0]).success(function (response)
        {
            settings[0].count = response[0].count;
            response.splice(0,1);
            settings[0].list = response;
        });
        $http.post(router, actions[1]).success(function (response)
        {
            settings[1].count = response[0].count;
            response.splice(0,1);
            settings[1].list = response;
        });
        $http.post(router, actions[2]).success(function (response)
        {
            settings[2].count = response[0].count;
            response.splice(0,1);
            settings[2].list = response;
        });
        $http.post(router, actions[3]).success(function (response)
        {
            settings[3].count = response[0].count;
            response.splice(0,1);
            settings[3].list = response;
        });
        $http.post(router, actions[4]).success(function (response)
        {
            settings[4].count = response[0].count;
            response.splice(0,1);
            settings[4].list = response;
        });

        deffered.resolve();
        return deffered.promise;
    };

    service.getSettings = function() {
        return settings;
    };

    service.setSelected = function(s) {
        selected = s;
    };

    service.getSelected = function() {
        return selected;
    };

    service.getSelectedSetting = function() {
        return selectedSetting;
    };

    service.setSelectedSetting = function(s) {
        selectedSetting = s;
    };

    service.saveData = function() {
        var deffered = $q.defer();
        var data = {};

        switch(selectedSetting.name) {
            case 'Update Grad Program':
                data["action"] = "updateGrad";
                break;
            case "Update Curriculum":
                data["action"] = "updateCurr";
                break;
            case "Update Buckets":
                data["action"] = "updateBuckets";
                break;
            case "Update Courses":
                data["action"] = "updateCourses";
                break;
            case "Update Student":
                data["action"] = "updateStudents";
                break;
        }

        data["list"] = selected;
        $http.post(router, data).success(function (response)
        {

        });
        deffered.resolve();
        return deffered.promise;
    };

    service.deleteData = function() {
        var deffered = $q.defer();
        var data = {};
        var i = 0;

        for (i=0; i<selectedSetting.count; i++)
        {
            var obj = selectedSetting.list[i];
            if (obj.name == selected.name) {
                selectedSetting.list.splice(i, 1);
                break;
            }
        }

        switch(selectedSetting.name) {
            case 'Update Grad Program':
                data["action"] = "updateGrad";
                break;
            case "Update Curriculum":
                data["action"] = "updateCurr";
                break;
            case "Update Buckets":
                data["action"] = "updateBuckets";
                break;
            case "Update Courses":
                data["action"] = "updateCourses";
                break;
            case "Update Student":
                data["action"] = "updateStudents";
                break;
        }

        selected["del"] = 1;
        data["list"] = selected;
        $http.post(router, data).success(function (response)
        {

        });
        deffered.resolve();
        return deffered.promise;
    };

    service.setStudent = function(s) {
        selectedStudent = s;
    };

    service.getStudent = function() {
        return selectedStudent;
    };

    service.setCourses = function() {
        var deffered = $q.defer();
        var data = {
            'action': "getStudentCourses",
            'list': selectedStudent};

        $http.post(router, data).success(function (response)
        {
            courses['list'] = response;

        });
        deffered.resolve();
        return deffered.promise;
    };

    service.getCourses = function() {
        return courses;
    };

    service.resetPassword = function() {
        var deffered = $q.defer();

        var data = {
            'action': "resetPass",
            'list': selectedStudent};

        $http.post(router, data).success(function (response)
        {

        });
        deffered.resolve();
        return deffered.promise;
    };

    service.getGrad = function() {
        var deffered = $q.defer();
        var data = {'action': "getGrad"};

        $http.post(router, data).success(function (response)
        {
            settings[0].count = response[0].count;
            response.splice(0,1);
            settings[0].list = response;
        });


        deffered.resolve();
        return deffered.promise;
    };

    service.updateCourse = function() {
        var deffered = $q.defer();
        var data = {'action': "getCourses"};

        $http.post(router, data).success(function (response)
        {
            settings[3].count = response[0].count;
            response.splice(0,1);
            settings[3].list = response;
        });


        deffered.resolve();
        return deffered.promise;
    };


    return service;

}]);

myApp.service('fileUpload', ['$http', '$mdDialog', function ($http, $mdDialog) {
    this.uploadFileToUrl = function(file, uploadUrl){
        var fd = new FormData();
        fd.append('action', 'importReq');
        fd.append('file', file);
        $http.post(uploadUrl, fd, {transformRequest: angular.identity,headers: {'Content-Type': undefined}})
            .success(function(response){
                if (response[0] = "success") {
                    alert = $mdDialog.alert()
                        .textContent('Imported Successfully')
                        .ok('Close');
                    $mdDialog
                        .show(alert)
                        .finally(function () {
                            alert = undefined;
                        });
                }
                else {
                    alert = $mdDialog.alert()
                        .textContent('Error Importing File')
                        .ok('Close');
                    $mdDialog
                        .show(alert)
                        .finally(function () {
                            alert = undefined;
                        });
                }

            })
            .error(function(){
            });
    }
}]);

myApp.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;

            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
    };
}]);