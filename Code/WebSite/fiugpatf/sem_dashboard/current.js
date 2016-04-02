
$(document).ready(function() {
    start();
} );

var currCourse;
var breakdown = null;
var $dialog;
var breakRowNum;
var router = 'semesterDashboardRouter.php';

function start() {
    $('#current_course').html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="ajax1"></table>');

    var noGrades;
    $.ajax({
        type: 'POST',
        async: false,
        url: router,
        data: {
            action: 'currentAssessments'
        },
        dataType: 'json',
        success: function (data) {
            currCourse = $('#ajax1').dataTable({
                "aaData": data,
                "aoColumns": [
                    {"sTitle": "Course ID"},
                    {"sTitle": "Course Name"},
                    {"sTitle": "Credits"},
                    {"sTitle": "Current Grade"}
                ],
                "bJQueryUI": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers"
            });
            $('#ajax1 tbody tr td').off();
            $('#ajax1 tbody tr td').on('click', curr_rowClickHandler);

            if(data.length === 0) {
                noGrades = true;
            }
            else {
                for (var i = 0; i < data.length; i++) {
                    if(data[i][3] != "No Grades") {
                        noGrades = false;
                        break;
                    }
                    else {
                        noGrades = true;
                    }
                }
            }
        }

    });

    if(!noGrades) {
        $('#current_course').append('<br><h2 id="gtitle">Grade Trends for All Courses</h2>');
        $('#current_course').append('<div id="placeholder"></div>');
        //$('#current_course').append('<button type="button" id="ExportButton">Export Data</button>');

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

                var legend = "<div id='graph_legend'><br><ul class='legend'>";
                for(var q = 0; q < courseLegend.length; q++) {
                    legend += "<li><span class='course" + q + "'></span>" + courseLegend[q] + "</li>";
                }
                legend += "</ul><br></div>";

                $('#current_course').append(legend);
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
	//$('#ExportButton').click(function(){
	//	exportData();
	//});

    $("#generateSemesterForecast").click(function() {

        setTimeout(function() {
            CheckSemesterForecast();
        }, 1000);

    });

    var graddata = new Array();
    $("#gradprogs").change(function() {
        for (var i = 0; i < graddata.length; i++) {
            if ($("#gradprogs").val() == graddata[i][0]) {
                //$('#data p').append(graddata[i][1]);
                $("#data p:first").replaceWith('<p>' + graddata[i]
                        [1] + '</p>');
                //var reqGrd = parseInt(graddata[i][1]);
                var reqGrdtext = $('#data p:first').text();
                var curGPAtext = $('#GPACalc').text();
                var curGPA = parseFloat(curGPAtext);
                var reqGrd = parseFloat(reqGrdtext);
            }
        }
    });

    $.ajax({
        type: 'POST',
        async: false,
        url: router,
        dataType: 'json',
        data: {
            action: 'getGradProgram'
        },
        success: function(data) {
            graddata = data;
            for (var i = 0; i < data.length; i++) {
                var cs = data[i][0];
                $("#gradprogs").append('<option value ="' + cs +
                    '">' + cs + '</option>');
            }
            for (var i = 0; data.length; i++) {
                if ($("#gradprogs").val() == data[i][0]) {
                    $('#data p').append(data[i][1]);
                }
            }
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
            action: 'getCurrentProgram'
        },
        success: function(data) {
            $('#studentMajData p').append(data[0][0]);
            $('#studentMajData p').replaceWith('<p>' + data[0][0] + '</p>');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function exportData()
{
	$.ajax({
        type: 'POST',
        url: 'getCurrCourse.php',
        data: {
            action: 'exportData'},
        dataType: 'text',
        success: function(data) {
			if(data.length > 0){
				download('export.sql', data);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
		    alert(errorThrown);
		}
	});
}
function curr_rowClickHandler(){
    var nTr = this.parentNode;
    var open= false;

    try{
        if($(nTr).next().children().first().hasClass("ui-state-highlight"))
            open=true;
    }catch(err){
        alert(err);
    }

    if (open){
        //This row is already open - close it
        currCourse.fnClose( nTr );
        $(nTr).css("color","");
    }else{
        curr_openDetailsRow(nTr);
    }
}

function curr_openDetailsRow(nTr){
    currCourse.fnOpen( nTr, curr_formatStoreManagerDetails(currCourse, nTr), "ui-state-highlight" );
    var aData = currCourse.fnGetData( nTr );
    $("#Remove"+aData[0]).button();
    $("#Breakdown"+aData[0]).button();
    $("#Grades"+aData[0]).button();

    var divId = "#courseDetails"+aData[0];
    $("#Remove"+aData[0]).click(function(){
        /*$(nTr).css("color","#c5dbec");
        $(divId).empty();
        curr_openForm(divId, false, nTr, aData[0]);*/
        remove_course(aData[0], nTr);

    });


    $("#Breakdown"+aData[0]).click(function(){
        location.href = 'breakdown.html?id=' + aData[0];
    });
}

function curr_formatStoreManagerDetails ( oTable, nTr )
{
    var aData = oTable.fnGetData( nTr );
    var id = aData[0];
    var sOut = '';
    sOut += '<div id="courseDetails'+id+'">';
    sOut += '    <div class="buttonColumnDetails">';
    sOut += '        <button id="Remove'+id+'" class="breakdownButton">Remove Course</button><br>';
    sOut += '        <button id="Breakdown'+id+'" class="breakdownButton">Assessment Breakdown</button><br>';
    sOut += '    </div>';
    sOut += '</div>';
    return sOut;
}

function remove_course(id, nTr) {
    var del = confirm("Delete course?");
    if (del == true) {
        $.ajax({
            type: 'POST',
            url: 'getCurrCourse.php',
            data: {
                action: 'remove',
                id: id
            },
            dataType: 'text',
            success: function (data) {
                if (data == 'true') {
                    currCourse.fnClose(nTr);
                    currCourse.fnDeleteRow(nTr);
                }
                else {
                    alert("Error: Course was not removed.");
                }
            }
        });
    }
}

function add_breakdown(course)
{
    if(breakdown == null)
    {
        var div =create_div(course);
        $dialog = $(div);

        $dialog.dialog({
            modal: true,
            width: 600
        });

        breakdown = $('#add_breakdown').dataTable({
            "aoColumns": [
                {"sTitle": "Name"},
                {"sTitle": "Percentage"},
            ],
            "bJQueryUI": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers"
        });
    }
    else{
        $dialog.dialog({
            modal: true,
            width: 600
        });
        breakdown.clear();
        //breakdown.draw();
    }
    breakRowNum = 0;
    addBreakRow(breakRowNum);

    $('#addRow').click(function(){
        addBreakRow();
    });
}

function addBreakRow()
{
    var display = new Array();
    display.push('<input type="text" name="name' + breakRowNum +'">');
    display.push('<input type="text" name="per' + breakRowNum + '">');
    breakdown.fnAddData(display);
    breakRowNum++;
}

function create_div(course)
{
    var sout = '';
    sout += '<div id="dialog" title="Add Breakdown for ' + course + '">';
    sout += '    <form id="formDialog">';
    sout += '        <table cellpadding="0" cellspacing="0" border="0" class="display" id="add_breakdown"></table>';
    sout += '        <input type="text" name="action" style="display: none" value="add_breakdown" id="actionText">';
    sout += '        <button type="button" id="addRow">Add Row</button>';
    sout += '        <button type="button" id="removeRow">Remove Row</button>';
    sout += '        <input type="button" onclick="breakdown_submit()" value="Submit">';
    sout += '    </form>';
    sout += '</div>';
    return sout;
}

function breakdown_submit()
{
    $('#dialog').dialog('close');
    /*
    var pvars = new Array();
    for(var i= 0; i<breakRowNum; i++)
    {
        pvars.push($(['[name ="name' + course + '"]']).val());
        pvars.push($(['[name ="name' + course + '"]']).val());
    }

    $.ajax({
        type: 'POST',
        url: 'getCurrCourse.php',
        data: {
            action: 'CurrentAssessments'},
        dataType: 'text',
        success: function(){
            alert("Breakdown Added");
        }
    });
    */
    breakRowNum = 0;
}

function download(filename, text) {
   var pom = document.createElement('a');
   pom.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
   pom.setAttribute('download', filename);    
   if (document.createEvent) {
       var event = document.createEvent('MouseEvents');
       event.initEvent('click', true, true);
       pom.dispatchEvent(event);
   }
   else {
       pom.click();
   }
}

function CheckSemesterForecast() {
    var creditsLeft = 0;
    var GPAGoal = 0;
    var totalGradePoints = 0;
    var allCourseCredits = 0;
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

            if(data == 'No courses remaining') { //check if values are null
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


        if(GPAGoal > maxGoalGPA) { //DO NOT run report - goal unattainable
            alert("Goal GPA of " + GPAGoal + " cannot be obtained by the time of graduation.\nPlease speak to your adviser for further assistane.");
        }
        else { //GPA Goal is attainable
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

            for(var i = 0; i < weight.length; i++) {
                if(relevance[i] <= 0 || weight[i] <= 0 || relevance[i] > 3 || weight[i] > 0) { //check to see if either relevance or weight = 0 or >3

                    if((relevance[i] <= 0 && weight[i] <= 0) || (relevance[i] > 3 && weight[i] > 0)) { //if both = 0 or 3, prompt for both values
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
                    else if(weight[i] <= 0 || weight[i] > 3) { //if only weight = 0 or > 3, prompt for weight
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

            alert("Ready For Forecast");
            window.open("semesterForecastReport.html", "Semester GPA Forecast Report");   // Opens a new window
        }
    } else {
        alert("No classes are available to generate semester forecast.\n Please speak to an adviser for further assistance.");
    }

}