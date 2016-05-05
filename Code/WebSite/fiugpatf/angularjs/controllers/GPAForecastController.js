/**
 * Created by Lizette Mendoza on 4/28/16.
 */

angular.module('GPAForecastApp', ['ngMaterial', 'md.data.table', 'chart.js'])

    .service('forecastService', ['$q', '$http', function ($q, $http) {
        var service = {};

        var router = 'OvrlDashRouter.php';
        var settings = [
            {
                name: 'GPAGoal'
            },
            {
                name: 'takenAndRemaining'
            },
            {
                name: 'gradesAndCredits'
            },
            {
                name: 'remainingCourses'
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
                },
                {
                    'action': "gradesAndCredits"
                },
                {
                    'action': "remainingCourses"
                }
            ];

            $http.post(router, actions[0]).success(function (response) {
                settings[0].list = response;
            });
            $http.post(router, actions[1]).success(function (response) {
                settings[1].list = response;
            });
            $http.post(router, actions[2]).success(function (response) {
                settings[2].list = response;
            });
            $http.post(router, actions[3]).success(function (response) {
                settings[3].list = response;
                deffered.resolve();
            });

            return deffered.promise;
        };

        service.getSettings = function() {
            return settings;
        };

        return service;
    }])

    .controller("graphController", ['$scope', '$http', function ($scope, $http) {

        var data = {
            'action' : "getGraphData"
        };

        $http.post('OvrlDashRouter.php',data).success( function(response) {
            //adding project labels to table
            response[0].push("P1**");
            response[0].push("P2**");
            response[0].push("P3**");

            var series = ['GPA AVERAGE AT FIU'];
            var projData = dataProjection(response[1]);

            $scope.labels = response[0];
            $scope.data = [projData];
            $scope.series = series;

            $scope.onClick = function (points, evt) {
                console.log(points, evt);
            };
        });
    }])

    .controller('GPATableController', ['$mdEditDialog', '$q', '$scope', '$http', 'forecastService', function ($mdEditDialog, $q, $scope, $http, forecastService) {

        var self = this;
        self.settings = [];
        $scope.list = [];
        var arr = [];


        $scope.selected = [];
        $scope.limitOptions = [5, 10, 15];

        $scope.query = {
            order: 'name',
            limit: 5,
            page: 1
        };

        $scope.options = {
            rowSelection: true
        };

        $scope.logItem = function (item) {
            console.log(item.name, 'was selected');
        };

        $scope.logOrder = function (order) {
            console.log('order: ', order);
        };

        forecastService.initial().then(function(){
            self.settings = forecastService.getSettings();
            console.log(self.settings[0].list);
            console.log(self.settings[1].list);
            console.log(self.settings[2].list);
            console.log(self.settings[3].list);

            arr = forecaster(self.settings);

            $scope.courses = { //data tables
                "data": arr
            };
            $scope.total = $scope.courses.data.length;
            //$scope.total = ;
        });
    }]);

//Weighted Moving Average - Projection
function dataProjection( data ) {
    var testData = data;
    var value = testData.length - 1;

    var count = 0;
    do {
        if (testData.length == 1) {
            //add value to inner array
            testData.push(testData[value]);
        }
        else if (testData.length == 2) {
            //avg of the two values
            var avg = ( (2*testData[value]) + (1*testData[value - 1]) ) / 3;
            //add value to inner array
            testData.push(avg);
        }
        else if (testData.length >= 3) {
            //grab last three values anf avg
            avg = ( (3*testData[value]) + (2*testData[value - 1]) + (1*testData[value - 2]) ) / 6;
            //add value to inner array
            testData.push(avg);
        }
        count++;
    } while(count < 3);

    return testData;
}

function forecaster( response ) {
    var arr = []; // array to be returned
    var totalGradePoints = 0;
    var allCourseCredits = 0;
    var totalCreditsRemaining;
    var accurateGPA;


    var data = response[0].list;
    var GPAGoal = data[0][0];

    data = response[1].list;
    var creditsLeft = parseInt(data[0][0]);

    var gradeChar;
    var gradeValue;
    var courseCredits;
    data = response[2].list;

    for(x = 0; x < data.length; x++) {
        gradeChar = data[x][0];
        courseCredits = parseInt(data[x][1]);

        switch (gradeChar) {
            case 'A':
                gradeValue = 4.00;
                break;
            case 'A-':
                gradeValue = 3.67;
                break;
            case 'B+':
                gradeValue = 3.33;
                break;
            case 'B':
                gradeValue = 3.00;
                break;
            case 'B-':
                gradeValue = 2.67;
                break;
            case 'C+':
                gradeValue = 2.33;
                break;
            case 'C':
                gradeValue = 2.00;
                break;
            case 'C-':
                gradeValue = 1.67;
                break;
            case 'D+':
                gradeValue = 1.33;
                break;
            case 'D':
                gradeValue = 1.00;
                break;
            case 'D-':
                gradeValue = 0.67;
                break;
            case 'F':
                gradeValue = 0.00;
                break;
            case 'F0*':
                gradeValue = 0.00;
                break;
            case 'P':
                gradeValue = 3.00;
            default:
                //error: char is not a Grade Value
                break;
        }
        totalGradePoints += (gradeValue * courseCredits);
        allCourseCredits += courseCredits;
    }

    accurateGPA = (totalGradePoints / allCourseCredits);

    var courseName = [];
    var courseID = [];
    var creditsIP = [];
    var relevance = [];
    var weight = [];
    totalCreditsRemaining= 0;

    data = response[3].list;

    for (x = 0; x < data.length; x++) {
        courseID.push(data[x][0]);
        courseName.push(data[x][1]);
        creditsIP.push(data[x][2]);
        weight.push(data[x][3]);
        relevance.push(data[x][4]);

        totalCreditsRemaining += data[x][2];
    }

    //breakdown GoalGPA to find the remaining amount of Grade Points needed to achieve GPA Goal
    var formattedGPAGoal = parseFloat(GPAGoal);
    var overallGradePoints = (formattedGPAGoal * (allCourseCredits + totalCreditsRemaining)) - totalGradePoints;

    //calculate estimatedGradePoints based on weight and relevance
    var relevanceUpdated = false;
    var relevanceMax = [];
    var lowestRelevance;
    var arrNum;
    var estimatedGradePoints;

    function remainingGradePoints() {
        estimatedGradePoints = 0;

        for (var i = 0; i < relevance.length; i++) {
            if (relevance[i] == 3.5) {
                relevanceMax[i] = 1; //1 means at MAX RELEVANCE
            }
            else {
                relevanceMax[i] = 0; //0 means NOT at MAX RELEVANCE
            }
        }

        for (var z = 0; z < relevance.length; z++) {
            var gradeValue = 0;

            switch (relevance[z]) {
                case 3.5:
                    gradeValue = 4.00;
                    break;
                case 3:
                    gradeValue = 3.67;
                    break;
                case 2.5:
                    gradeValue = 3.33;
                    break;
                case 2:
                    gradeValue = 3.00;
                    break;
                case 1.5:
                    gradeValue = 2.67;
                    break;
                case 1:
                    gradeValue = 2.33;
                    break;
                case 0:
                    gradeValue = 2.00;
                    break;
                default:
                    //error: relevance value is not a valid number
                    break;
            }

            estimatedGradePoints += (creditsIP[z] * gradeValue);
        }

        return estimatedGradePoints;
    }

    do {
        var EGP = remainingGradePoints();
        var successful = false;

        if(EGP < overallGradePoints) { //in theory, shouldn't enter if EGP is greater than SGP

            var maxedOut = true; //each class has reached a max grade of 4.0
            lowestRelevance = 0;
            arrNum = 0;

            for(j = 0; j < relevanceMax.length; j++) { //check to see if courses aren't maxedOut yet
                if (relevanceMax[j] == 0) { //there exists a class that is not maxedOut
                    lowestRelevance = relevance[j];
                    arrNum = j;
                    maxedOut = false;
                    break;
                }
            }

            if(maxedOut) { //all courses have been maxedOut, so break the do-while
                break;
            }

            for(var x = arrNum; x < relevance.length; x++) { //loop starts at first non-maxedOut value
                if(relevanceMax[x] != 1) {
                    if(lowestRelevance > relevance[x]) {
                        lowestRelevance = relevance[x];
                        arrNum = x;
                    }
                }
            }

            //increase relevance value
            if(relevance[arrNum] == 0) {
                relevance[arrNum] = 1;
                relevanceUpdated = true;
            }
            else {
                //alert("Time to update!")
                relevance[arrNum] += 0.5;
                relevanceUpdated = true;
                if(relevance[arrNum] == 3.5) {
                    relevanceMax[arrNum] = 1;
                }
            }
        }
        else {
            successful = true;
        }
    } while(!successful);

    //calculate secureGPAPath
    var secureGPAPath = [];
    for(i = 0; i < relevance.length; i++) {
        if(relevance[i] <= 3) {
            secureGPAPath[i] = relevance[i] + 0.5;
        }
        else {
            secureGPAPath[i] = relevance[i];
        }
    }

    function valueToChar(value) {
        var letter;
        switch (value) {
            case 3.5:
                letter = 'A';
                break;
            case 3:
                letter = 'A-';
                break;
            case 2.5:
                letter = 'B+';
                break;
            case 2:
                letter = 'B';
                break;
            case 1.5:
                letter = 'B-';
                break;
            case 1:
                letter = 'C+';
                break;
            case 0:
                letter = 'C';
                break;
            default:
                //error: relevance value is not a valid number
                break;
        }
        return letter;
    }

    //calculate minimumStudyTime from (relevance * weight)
    var minimumStudyTime = [];
    for(var j = 0; j < relevance.length; j++) {
        minimumStudyTime[j] = Math.floor(relevance[j]) * weight[j];
    }

    //Appending the first half of the report
    var sum = '<div class="heading" layout="row">' +
        '<span flex></span><div class="text" layout="column" layout-align="center start" flex="20">' +
        '<strong>Current GPA:</strong><br>' +
        '<strong>Graduation Goal GPA:</strong><br>' +
        '<strong>Credits Remaining:</strong>' +
        '</div>' +
        '<div class="results" layout="column" layout-align="center end" flex="5">' +
        accurateGPA.toFixed(2) + '<br>' +
        formattedGPAGoal.toFixed(2) + '<br>' +
        '<span class="credits">' + creditsLeft + '</span>' +
        '</div><span flex></span></div>' +
        '<br>' +
        '<p class="inorder">In order to your Graduation Goal GPA of ' + formattedGPAGoal.toFixed(2) + ', the following forecast has been generated according to the weight and relevance you provided for all the remaining courses:</p>';

    var myEl = angular.element( document.querySelector( '#sum' ) );
    myEl.append(sum);

    for(var i = 0; i < courseID.length; i++) {
        arr.push({
            "class": courseID[i],
            "weight": weight[i],
            "relevance": Math.floor(relevance[i]),
            "grade": valueToChar(relevance[i]),
            "secure": valueToChar(secureGPAPath[i]),
            "time": minimumStudyTime[i]
        });
    }

    return arr;
}