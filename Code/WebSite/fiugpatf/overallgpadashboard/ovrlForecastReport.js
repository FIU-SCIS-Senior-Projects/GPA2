/**
 * Created by Lizette Mendoza on 3/18/16.
 */

$(document).ready(function() {
    GenerateForecast();
});

function GenerateForecast() {
    var router = 'OvrlDashRouter.php';
    var creditsLeft = 0;
    var GPAGoal = 0;
    var totalGradePoints = 0;
    var allCourseCredits = 0;
    var totalCreditsRemaining;
    var accurateGPA;


    $.ajax({
        type: 'POST',
        async: false,
        url: router,
        dataType: 'json',
        data: {
            action: 'GPAGoal'
        },
        success: function(data) {

            GPAGoal = data[0][0];

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });

    $.ajax({
        type: 'POST',
        async: false,
        url: router,
        dataType: 'json',
        data: {
            action: 'takenAndRemaining'
        },
        success: function(data) {
             creditsLeft = parseInt(data[0][0]);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });

    $.ajax({
        type: 'POST',
        async: false,
        url: router,
        dataType: 'json',
        data: {
            action: 'gradesAndCredits'
        },
        success: function(data2) {
            var gradeChar;
            var gradeValue;
            var courseCredits;
            totalGradePoints = 0;
            allCourseCredits = 0;

            for(var x = 0; x < data2.length; x++) {
                gradeChar = data2[x][0];
                courseCredits = parseInt(data2[x][1]);

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

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });

    accurateGPA = (totalGradePoints / allCourseCredits);

    var courseName = new Array();
    var courseID = new Array();
    var creditsIP = new Array();
    var relevance = new Array();
    var weight = new Array();
    totalCreditsRemaining= 0;

    $.ajax({
        type: 'POST',
        async: false,
        url: router,
        dataType: 'json',
        data: {
            action: 'remainingCourses'
        },
        success: function (data21) {
            for (var x = 0; x < data21.length; x++) {
                courseID.push(data21[x][0]);
                courseName.push(data21[x][1]);
                creditsIP.push(data21[x][2]);
                weight.push(data21[x][3]);
                relevance.push(data21[x][4]);

                totalCreditsRemaining += data21[x][2];
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });

    //breakdown GoalGPA to find the remaining amount of Grade Points needed to achieve GPA Goal
    var formattedGPAGoal = parseFloat(GPAGoal);
    var overallGradePoints = (formattedGPAGoal * (allCourseCredits + totalCreditsRemaining)) - totalGradePoints;

    //calculate estimatedGradePoints based on weight and relevance
    var relevanceUpdated = false;
    var relevanceMax = new Array();
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
    var secureGPAPath = new Array();
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
    var minimumStudyTime = new Array();
    for(var j = 0; j < relevance.length; j++) {
        minimumStudyTime[j] = Math.floor(relevance[j]) * weight[j];
    }

    //Appending the first half of the report
    $("#sum").append('<p id="heading" align="center"><strong>Current GPA:</strong> ' + accurateGPA.toFixed(2) + '<br><strong>Graduation Goal GPA:</strong> ' + formattedGPAGoal.toFixed(2) + '<br><strong>Credits Remaining:</strong> ' + creditsLeft + '<br></p><p>In order to your Graduation GPA Goal of ' + formattedGPAGoal.toFixed(2) + ', the following forecast has been generated according to the weight and relevance you provided for all the remaining courses:</p>');

    var content = "<table><tr><th>Class</th><th>Weight</th><th>Relevance</th><th>Min. Grade<br>Required</th><th>Secure<br>GPA Path</th><th>Estimated Study<br>Time (Hrs/Week)*</th></tr>";

    for(var i = 0; i < courseID.length; i++) {
        content += ("<tr><td>" + courseID[i] + "</td>" +
        "<td>" + weight[i] + "</td>" +
        "<td>" + Math.floor(relevance[i]) + "</td>" +
        "<td>" + valueToChar(relevance[i]) + "</td>" +
        "<td>" + valueToChar(secureGPAPath[i]) + "</td>" +
        "<td>" + minimumStudyTime[i] + "</td></tr>");
    }
    content += "</table>";

    $("#forecast_table").append(content);

    $("#recommend").append('<p><i>*While only a recommendation, we highly recommend students to consider their circumstances and select an appropriate schedule based on their workload.</i></p>');


    //graph
    setTimeout(function() {
        getGraph();
    }, 1000);

    function getGraph() {
        $.ajax({
            type: 'POST',
            url: 'OvrlDashRouter.php',
            dataType: 'json',
            data: {
                action: 'getGraphData'
            },
            success: function(data) {

                var ticks = [];
                var avg = [];
                for(var i = 0; i < data.length; i++) {
                    ticks.push([i, data[i][0]]);
                    avg.push([i, data[i][1]]);
                }

                var data2 = [avg];

                $.plot($('#placeholder'), data2, {
                    xaxis: {
                        axisLabel: "Semester",
                        ticks: ticks
                    },
                    yaxis: {
                        axisLabel: "GPA",
                        min: 2,
                        max: 4
                    },
                    series: {
                        points: {
                            radius: 3
                        }
                    },
                    colors: ["#000080"]
                });
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }

}