
$(document).ready(function() {
    GenerateSemesterForecast();
    start();
} );

var currCourse;
var breakdown = null;
var router = 'semesterDashboardRouter.php';

function start() {

        $('#graph_courses').append('<h2 id="gtitle">Grade Trends for All Courses</h2>');
        $('#graph_courses').append('<div id="placeholder"></div>');

        var courseLegend = new Array();
        $.ajax({
            type: 'POST',
            async: false,
            url: router,
            data: {
                action: 'courseLegend'
            },
            dataType: 'json',
            success: function (data) {
                for (var x = 0; x < data.length; x++) {
                    courseLegend.push(data[x]);
                }

                //create graph legend
                var legend = "<div id='graph_legend'><br><ul class='legend'>";
                for(var q = 0; q < courseLegend.length; q++) {
                    legend += "<li><span class='course" + q + "'></span>" + courseLegend[q] + "</li>";
                }
                legend += "</ul><br></div>";

                $("#graph_courses").append(legend);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
        $.ajax({
            type: 'POST',
            async: false,
            url: router,
            data: {
                action: 'getGraphData'
            },
            dataType: 'json',
            success: function (data) {

                $.plot($('#placeholder'), data.slice(0, data.length), {
                    xaxis: {
                        axisLabel: "Date",
                        mode: "time",
                        timeformat: "%m-%d"
                    },
                    yaxis: {
                        axisLabel: "Running Grade",
                        min: 50,
                        max: 100
                    },
                    series: {
                        points: {
                            radius: 3
                        }
                    },
                    colors: ["#000080", "#FFD700", "#333333", "#0f6b2e", "#ff3300", "#7d00b3"]
                });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

}

function GenerateSemesterForecast() {
    var creditsLeft = 0;
    var GPAGoal = 0;
    var totalGradePoints = 0;
    var allCourseCredits = 0;
    var creditsInProgress;
    var accurateGPA;
    var classesImported = true;


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

            if(data[0][0] === null) { //check if values are null
                classesImported = false;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });

    if (classesImported) {

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

        //calculate maxGoalGpa
        var maxGoalGPA = ((totalGradePoints +(creditsLeft * 4)) / (allCourseCredits + creditsLeft)).toFixed(2);
        accurateGPA = (totalGradePoints / allCourseCredits);
        var formattedGPAGoal = parseFloat(GPAGoal);

        if(formattedGPAGoal > maxGoalGPA) {
            //alert("Goal GPA of " + GPAGoal + " cannot be obtained by the time of graduation.\nPlease speak to your adviser for further assistance.");
        }
        else {

            var courseName = new Array();
            var courseID = new Array();
            var creditsIP = new Array();
            var relevance = new Array();
            var weight = new Array();
            creditsInProgress = 0;

            $.ajax({
                type: 'POST',
                async: false,
                url: router,
                dataType: 'json',
                data: {
                    action: 'currentCourses'
                },
                success: function (data21) {
                    for (var x = 0; x < data21.length; x++) {
                        courseID.push(data21[x][0]);
                        courseName.push(data21[x][1]);
                        creditsIP.push(data21[x][2]);
                        weight.push(data21[x][3]);
                        relevance.push(data21[x][4]);

                        creditsInProgress += data21[x][2];
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });

            //breakdown GoalGPA over remaining semesters assuming student takes 4 classes per semester
            var GPARemainingForGoal = formattedGPAGoal - accurateGPA;
            var semestersRemaining = Math.ceil(creditsLeft / 12);
            var semesterGoal = accurateGPA + (GPARemainingForGoal / semestersRemaining);
            var semesterGradePoints = (formattedGPAGoal * (allCourseCredits + creditsInProgress)) - totalGradePoints;

            //calculate estimatedSemesterGradePoints based on weight and relevance
            var relevanceUpdated = false;
            for(i = 0; i < weight.length; i++) {
                if(relevance[i] == 0 || weight[i] == 0) { //check to see if either relevance or weight = 0

                    if(relevance[i] == 0 && weight[i] == 0) { //if both = 0, prompt for both values
                        relevance[i] = prompt("Enter Relevance (0-3) for " + courseName[i] + " - " + courseID[i], "0 = No Relevance, 3 = Completely Relevant");
                        weight[i] = prompt("Enter Weight (1-3) for " + courseName[i] + " - " + courseID[i], "1 = Easy, 3 = Hard");
                        $.ajax({
                            type: 'POST',
                            async: false,
                            url: router,
                            dataType: 'json',
                            data: {
                                action: 'modifyWeightAndRelevance',
                                courseID: courseID[i],
                                modifiedRelevance: relevance[i],
                                modifiedWeight: weight[i]
                            },
                            success: function (data) {

                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                alert(errorThrown);
                            }
                        });
                    }
                    else if(weight[i] == 0) { //if only weight = 0, prompt for weight
                        weight[i] = prompt("Enter Weight for " + courseName[i] + " - " + courseID[i], "1 = Easy, 3 = Hard");
                        $.ajax({
                            type: 'POST',
                            async: false,
                            url: router,
                            dataType: 'json',
                            data: {
                                action: 'modifyWeightAndRelevance',
                                courseID: courseID[i],
                                modifiedRelevance: relevance[i],
                                modifiedWeight: weight[i]
                            },
                            success: function (data) {

                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                alert(errorThrown);
                            }
                        });
                    }
                }
            }

            var relevanceMax = new Array();
            var lowestRelevance;
            var arrNum;
            var estimatedGradePoints;

            function estimatedSemesterGradePoints() {
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
                var EGP = estimatedSemesterGradePoints();
                var successful = false;

                if(EGP < semesterGradePoints) { //in theory, shouldn't enter if EGP is greater than SGP

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

            $("#sum").append('<p id="heading" align="center"><strong>Current GPA:</strong> ' + accurateGPA.toFixed(2) + '<br><strong>Graduation Goal GPA:</strong> ' + formattedGPAGoal.toFixed(2) + '<br><strong>Semester Goal GPA:</strong> ' + semesterGoal.toFixed(2) + '<br><strong>Credits Remaining:</strong> ' + creditsLeft + '<br></p><p>In order to your Graduation Goal GPA, the following forecast has been generated according to the weight and relevance you provided:</p>');

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

        }
    } else {
        alert("No classes are available to generate semester forecast.\n Please speak to an adviser for further assistance.");
    }

}