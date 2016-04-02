var router = 'OvrlDashRouter.php';

$(document).ready(function() {
    start2();
});

function toggle() {
    $("#coursesTaken tbody td:nth-child(3) ").toggle();
    $(".GPACalcBox p:nth-child(2)").toggle();
}


var childTableDTNeededCourses;
var childTableDTNeeded;
var childTableDT;
var childTableTakenE
var parentTable;
var parentTableTaken;
var childTable1;
var childTable2;
var courseNeeded;
var coursesNeededDT;
var coursesTakenDT;
var courseTaken;
var courseTakenBuckets;
var courseNeededBuckets;
var nCID = "";
var nCredits = "";
var nGrade = "";
var nMajor = "";
var noes = 0;
var met = 0;
var gpa = 0;
var creditsTaken;
var creditsLeft;
var GPAGoal = 0;
var GradeNeeded = 0;
var GradeNeededFormatted = 0;
var totalGradePoints;
var allCourseCredits;
var creditsInProgress;
var accurateGPA;


function sto_formatStoreManagerDetails(oTable, nTr) {
    var aData = oTable.fnGetData(nTr);
    var id = aData[0];
    var sOut = '';
    sOut += '<div id="itemDetails' + id + '">';
    sOut += '	<div class="buttonColumnDetails">';
    sOut += '		<button id="modifyItem' + id + '">Modify</button>';
    sOut += '		<button id="deleteItem' + id + '">>>></button>';
    sOut +=
        '		<div id = "pop" style = "display: none" title="Modify Grade" > ';
    sOut += '			<form method = "post" name = "newcourseID">';
    sOut += '				<label for="nGrade">Grade:</label>';
    sOut +=
        '				<input id = "nGrade" style="margin-left: 90px;" placeholder =" New Grade " size	= "8" type="text" name="nGrade"><br><br>';

    sOut += '				<button id = "modSubmit" type="button">Submit</button>';
    sOut += '			</form>';
    sOut += '		</div>';
    sOut += '	</div>';
    sOut += '</div>';
    return sOut;
}

function sto_formatDataTable(oTable, nTr) {
    var aData = oTable.fnGetData(nTr);
    var id = removeSpace(aData[1]);
    var sOut = '';
    sOut += '<table id ="coursesTakenDT' + id + '">';
    sOut += '<thead><tr><th></th><th></th><th></th></tr></thead>';
    sOut += '<tbody></tbody></table>';


    return sOut;
}

function sto_formatDataTableNeeded(oTable, nTr) {
    var aData = oTable.fnGetData(nTr);
    var id = removeSpace(aData[1]);
    var sOut = '';
    sOut += '<table id ="coursesNeededDT' + id + '">';
    sOut += '<thead><tr><th></th><th></th><th></th><th></th></tr></thead>';
    sOut += '<tbody></tbody></table>';


    return sOut;
}

function sto_formatDataTableNeeded2(oTable, nTr) {
    var aData = oTable.fnGetData(nTr);
    var id = removeSpace(aData[1]);
    var sOut = '';
    sOut += '<table id ="coursesNeededDT' + id + '">';
    sOut += '<thead><tr><th></th><th></th><th></th><th></th><th></th></tr></thead>';
    sOut += '<tbody></tbody></table>';


    return sOut;
}

function sto_formatDataTableNeededChildBuckets(oTable, nTr) {
    var aData = oTable.fnGetData(nTr);
    var id = removeSpace(aData[1]);
    var sOut = '';
    sOut += '<table id ="childBucketsDT' + id + '">';
    sOut += '<thead><tr><th></th><th></th><th></th></tr></thead>';
    sOut += '<tbody></tbody></table>';


    return sOut;
}

function sto_formatDataTableTakenChildBuckets(oTable, nTr) {
    var aData = oTable.fnGetData(nTr);
    var id = removeSpace(aData[1]);
    var sOut = '';
    sOut += '<table id ="childBucketsDTTaken' + id + '">';
    sOut += '<thead><tr><th></th><th></th><th></th></tr></thead>';
    sOut += '<tbody></tbody></table>';


    return sOut;
}

function sto_formatDataTableNeededChildBuckets2(oTable, nTr) {
    var aData = oTable.fnGetData(nTr);
    var id = aData[1];
    var sOut = '';
    sOut += '<table id ="childBucketsDT2">';
    sOut += '<thead><tr><th></th><th>subbuckets</th><th>all required</th></tr></thead>';
    sOut += '<tbody></tbody></table>';


    return sOut;
}

function sto_formatDataTableNaturalScience(oTable, nTr) {
    var aData = oTable.fnGetData(nTr);
    var id = removeSpace(aData[1]);
    var sOut = '';
    sOut += '<table id ="coursesNeededDT' + id + '">';
    sOut += '<thead><tr> <td><form action = "OvrlDash.php" method = "post" name = "courseID"><input id = "addExtraCourse" placeholder ="Course ID" size	= "8" type="text" name="courseAdded"></form></td> <td><input id ="addECButton" type = "submit" name = "Add" value = "Add" ></td> </tr><tr><th></th><th></th><th></th><th></th><th></th></tr></thead>';
    sOut += '<tbody></tbody></table>';


    return sOut;
}

function sto_formatStoreManagerDetails2(oTable, nTr) {
    var aData = oTable.fnGetData(nTr);
    var id = aData[0];
    var sOut = '';
    sOut += '<div id="itemDetails' + id + '">';
    sOut += '	<div class="buttonColumnDetails">';
    sOut += '		<button id="moveItem' + id + '"><<<</button>';
    sOut += '		<button id="modifyItem' + id + '">Modify</button>';
    sOut +=
        '		<div id = "pop2" style = "display: none" title="Modify Weight and Relevance" > ';
    //sOut += '			<p>ENTER NEW COURSE DETAILS</p>';
    sOut +=
        '			<form method = "post" id = "newGrade" name = "newcourseID">';
    sOut += '			   <label for="nWeight">Weight:    </label>';
    sOut +=
        '				<input style="margin-left: 60px;" id = "nWeight" placeholder =" New Weight" size	= "8" type="text" name="nWeight"><br>';
    sOut += '				<label for="nRelev">Relevance: </label>';
    //sOut += '				<input id = "nCredits" placeholder =" New Credits " size	= "8" type="text" name="nCredits"><br>';
    sOut +=
        '				<input style="margin-left: 31px;" id = "nRelev" placeholder ="New Relevance " size	= "8" type="text" name="nRelev"><br><br>';
    //sOut += '				<input id = "nMajor" placeholder =" New Major " size	= "8" type="text" name="nMajor"><br>';
    sOut += '				<button id = "modSubmit2" type="button">Submit</button>';
    sOut += '			</form>';
    sOut += '		</div>';
    sOut += '	</div>';
    sOut += '</div>';
    return sOut;
}

function sto_formatStoreManagerDetailsAdmin(oTable, nTr) {
    var aData = oTable.fnGetData(nTr);
    var id = aData[0];
    var sOut = '';
    sOut += '<div id="itemDetails' + id + '">';
    sOut += '	<div class="buttonColumnDetails">';

    sOut += '		<button id="modifyItem' + id + '">Modify</button>';
    sOut +=
        '		<div id = "pop2" style = "display: none" title="Modify Weight and Relevance" > ';
    //sOut += '			<p>ENTER NEW COURSE DETAILS</p>';
    sOut +=
        '			<form method = "post" id = "newGrade" name = "newcourseID">';
    sOut += '			   <label for="nWeight">Weight:    </label>';
    sOut +=
        '				<input style="margin-left: 60px;" id = "nWeight" placeholder =" New Weight" size	= "8" type="text" name="nWeight"><br>';
    sOut += '				<label for="nRelev">Relevance: </label>';
    //sOut += '				<input id = "nCredits" placeholder =" New Credits " size	= "8" type="text" name="nCredits"><br>';
    sOut +=
        '				<input style="margin-left: 31px;" id = "nRelev" placeholder ="New Relevance " size	= "8" type="text" name="nRelev"><br><br>';
    //sOut += '				<input id = "nMajor" placeholder =" New Major " size	= "8" type="text" name="nMajor"><br>';
    sOut += '				<button id = "modSubmit2" type="button">Submit</button>';
    sOut += '			</form>';
    sOut += '		</div>';
    sOut += '	</div>';
    sOut += '</div>';
    return sOut;
}

function sto_rowClickHandler() {
    var nTr = this.parentNode;
    var open = false;
    try {
        if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
            open = true;
    } catch (err) {
        alert(err);
    }
    if (open) {
        /* This row is already open - close it */
        childTableDT.fnClose(nTr);
        $(nTr).css("color", "");
    } else {
        sto_openDetailsRow(nTr);
    }
}

function sto_rowClickHandler2() {
    var nTr = this.parentNode;
    var open = false;
    try {
        if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
            open = true;
    } catch (err) {
        alert(err);
    }
    if (open) {
        /* This row is already open - close it */
        childTable1.fnClose(nTr);
        $(nTr).css("color", "");
    } else {
        sto_openDetailsRow2(nTr);
    }
}

function sto_rowClickHandler3() {
    var nTr = this.parentNode;
    var open = false;
    try {
        if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
            open = true;
    } catch (err) {
        alert(err);
    }
    if (open) {
        /* This row is already open - close it */
        studRoster.fnClose(nTr);
        $(nTr).css("color", "");
    } else {
        sto_openDetailsRow3(nTr);
    }
}

function sto_rowClickHandler4() {
    var nTr = this.parentNode;
    var open = false;
    try {
        if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
            open = true;
    } catch (err) {
        alert(err);
    }
    if (open) {
        /* This row is already open - close it */
        childTable2.fnClose(nTr);
        $(nTr).css("color", "");
    } else {
        sto_openDetailsRow4(nTr);
    }
}

function sto_rowClickHandler5() {
    var nTr = this.parentNode;
    var open = false;
    try {
        if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
            open = true;
    } catch (err) {
        alert(err);
    }
    if (open) {
        /* This row is already open - close it */
        childTableTakenE.fnClose(nTr);
        $(nTr).css("color", "");
    } else {
        sto_openDetailsRow5(nTr);
    }
}

function addArrow(oTable, nTr) {
    var bData = oTable.fnGetData(nTr);
    sto_addItem(bData[0], bData[1], 'IP', 'CS');
    oTable.fnDeleteRow(nTr);
}

function sto_openDetailsRow2(nTr) {
    childTable1.fnOpen(nTr, sto_formatStoreManagerDetails2(childTable1,
        nTr), "ui-state-highlight");
    var aData = childTable1.fnGetData(nTr);
    $("#modifyItem" + aData[0]).button();
    $("#moveItem" + aData[0]).button();
    var divId = "#itemDetails" + aData[0];
    $("#modifyItem" + aData[0]).click(function() {
        $("#pop2").dialog();
        $('#pop2').on('dialogclose', function(event) {
            childTable1.fnClose(nTr);
            (divId).empty();
            $(nTr).css("color", "#c5dbec");
            $("#pop2").remove();
        });
    });
    $("#modSubmit2").click(function() {
        nRelev = $("input[name=nRelev]").val();
        nWeight = $("input[name=nWeight]").val();
        sto_modWeight(childTable1, divId, nTr, nWeight, nRelev);
        childTable1.fnUpdate([aData[0], aData[1], nWeight, nRelev],
            nTr);
        $('#pop2').dialog('close');
    });
    $("#addArrow").click(function() {
        $(nTr).css("color", "#c5dbec");
        addArrow(childTable1, nTr);
    });
    $("#moveItem" + aData[0]).click(function() {
        $(nTr).css("color", "#c5dbec");
        addArrow(childTable1, nTr);
    });
}

function fnGPACalcReturn(grades, credits) {
    var gradepoints = 0;
    var credithours = 0;
    //	alert("grades.length: " + grades.length + " grsds[i]: " + grades[0]);
    for (var i = 0; i < grades.length; i++) {
        if (grades[i] == "A") {
            gradepoints = gradepoints + (4 * credits[i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "A-") {
            gradepoints = gradepoints + (3.67 *
                credits[i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "B+") {
            gradepoints = gradepoints + (3.33 *
                credits[i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "B") {
            gradepoints = gradepoints + (3.0 * credits[
                    i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "B-") {
            gradepoints = gradepoints + (2.67 *
                credits[i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "C+") {
            gradepoints = gradepoints + (2.33 *
                credits[i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "C") {
            gradepoints = gradepoints + (2 * credits[i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "C-") {
            gradepoints = gradepoints + (1.67 *
                credits[i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "D+") {
            gradepoints = gradepoints + (1.33 *
                credits[i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "D") {
            gradepoints = gradepoints + (1.0 * credits[
                    i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "D-") {
            gradepoints = gradepoints + (0.67 *
                credits[i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "F") {
            gradepoints = gradepoints + (0 * credits[i]);
            credithours = credithours + (1 * credits[i]);
        } else if (grades[i] == "IP") {
            gradepoints = gradepoints + (0 * credits[i]);
            credithours = credithours + (0 * credits[i]);
        } else {
            gradepoints = gradepoints + (0 * credits[i]);
            credithours = credithours + (0 * credits[i]);
        }
    }

    var thisGPA = gradepoints / credithours;
    thisGPA = Math.round(thisGPA * 100) / 100;
    return thisGPA;
}

function GetGPA() {
    $.ajax({
        type: 'POST',
        url: router,
        dataType: 'json',
        data: {
            action: 'getGPA'
        },
        success: function(data) {
            $("#GPACalc").replaceWith('<p id = "GPACalc" style = "font-size:16px;">' + data[0] + '</p>');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function removeSpace(string) {
    var substrings = string.split(" ");
    string = "";
    for (var i = 0; i < substrings.length; i++) {
        string = string.concat(substrings[i]);
    }
    return string;
}

function GenerateForecast() {

    var creditsLeft = 0;
    var GPAGoal = 0;
    var totalGradePoints = 0;
    var allCourseCredits = 0;
    var allSelected = true;
    var noGrades = false;
    var router = 'OvrlDashRouter.php';

    $.ajax({
        type: 'POST',
        async: false,
        url: router,
        dataType: 'json',
        data: {
            action: 'takenAndRemaining'
        },
        success: function(data) {

            if(data == 'No grades') { //check if values are null
                noGrades = true;
            }
            else {
                creditsLeft = parseInt(data[0][0]);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });


    if(noGrades) {
        alert("No classes are available to generate GPA forecast.\n Please speak to an adviser for further assistance.");
    }
    else {

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

                for (var x = 0; x < data2.length; x++) {

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
                            break;
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

        //calculate maxGoalGpa
        var maxGoalGPA = ((totalGradePoints +(creditsLeft * 4)) / (allCourseCredits + creditsLeft)).toFixed(2);
        var formattedGPAGoal = parseFloat(GPAGoal);

        if(GPAGoal > maxGoalGPA) { //DO NOT run report - goal unattainable
            alert("Goal GPA of " + formattedGPAGoal.toFixed(2) + " cannot be obtained by the time of graduation.\nThe maximum GPA Goal that can be obtained by graduation is " + maxGoalGPA + ".\nPlease speak to your adviser for further assistance.");
        }
        else {
            $.ajax({
                type: 'POST',
                async: false,
                url: router,
                dataType: 'json',
                data: {
                    action: 'checkWeightAndRelevance'
                },
                success: function(data) {

                    for (var x = 0; x < data.length; x++) {
                        if(data[x][1] == 0) { //check if weight or relevance are null
                            allSelected = false;
                            break;
                        }
                    }

                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });

            if(!allSelected) {
                alert("Select the weight and relevance for ALL\nin-progress and remaining courses.");
            }
            else {
                alert("Ready For Forecast");
                window.open("ovrlForecastReport.html", "Overall GPA Forecast Report");   // Opens a new window for GPA Forecast Report
            }
        }
    }
}

function RequirementMet(bucket) {
    $.ajax({
        type: 'POST',
        url: router,
        dataType: 'json',
        data: {
            action: 'findChildBuckets',
            bucket: bucket

        },
        success: function(data) {

            if (data.success) {
                // children buckets found
                GetBucketReqs(bucket);
            } else {
                // no child buckets found
                GetCourseReqs(bucket);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("course doesnt exist");
            alert(errorThrown);
        }
    });

}

function GetCourseReqs(bucket) {
    var OvrlDashphpURL = 'OvrlDash.php';

    $.ajax({
        type: 'POST',
        url: OvrlDashphpURL,
        dataType: 'json',
        data: {
            action: 'getMinReq',
            bucket: bucket
        },
        success: function(data) {
            var change = bucket;
            var changes = JSON.stringify(bucket);

            if (data[0][0] >= data[0][1]) {
                //if the td containing the name of the bucket doesnt have "requirement met" inside of it

                if ($("td:contains('" + change + "')").text().indexOf("Requirement") == -1) {

                    var elem = $("td:contains('" + change + "')");

                    elem.append('<p style = "font-size:9px;color:blue;" class = "appendReq' + bucket + '"> Requirement met</p>');
                }
            } else {
                $("td:contains('" + change + "')").children().remove();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function GetBucketReqs(bucket) {
    var OvrlDashphpURL = 'OvrlDash.php';

    $.ajax({
        type: 'POST',
        url: OvrlDashphpURL,
        dataType: 'json',
        data: {
            action: 'getMinBucketReq',
            bucket: bucket
        },
        success: function(data) {
            var change = bucket;
            var changes = JSON.stringify(bucket);

            if (data[0][0] >= data[0][1]) {
                if ($("td:contains('" + change + "')").text().indexOf("Requirement") == -1) {
                    var elem = $("td:contains('" + change + "')");
                    elem.append('<p style = "font-size:9px;color:blue;" class = "appendReq' + bucket + '"> Requirement met</p>');
                }
            } else {}
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function sto_openDetailsRow4(nTr) {
    childTable2.fnOpen(nTr, sto_formatStoreManagerDetails2(childTable2,
        nTr), "ui-state-highlight");
    var aData = childTable2.fnGetData(nTr);
    $("#modifyItem" + aData[0]).button();
    $("#moveItem" + aData[0]).button();
    var divId = "#itemDetails" + aData[0];
    $("#modifyItem" + aData[0]).click(function() {
        $("#pop2").dialog();
        $('#pop2').on('dialogclose', function(event) {
            childTable2.fnClose(nTr);
            (divId).empty();
            $(nTr).css("color", "#c5dbec");
            $("#pop2").remove();
        });
    });
    $("#modSubmit2").click(function() {
        nRelev = $("input[name=nRelev]").val();
        nWeight = $("input[name=nWeight]").val();
        sto_modWeight(childTable2, divId, nTr, nWeight, nRelev);
        childTable2.fnUpdate([aData[0], aData[1], nWeight, nRelev],
            nTr);
        $('#pop2').dialog('close');
    });
    $("#addArrow").click(function() {
        $(nTr).css("color", "#c5dbec");
        addArrow(childTable2, nTr);
    });
    $("#moveItem" + aData[0]).click(function() {
        $(nTr).css("color", "#c5dbec");
        addArrow(childTable2, nTr);
    });
}

function sto_openDetailsRow5(nTr) {

    childTableTakenE.fnOpen(nTr, sto_formatStoreManagerDetails(childTableTakenE, nTr),
        "ui-state-highlight");
    var aData = childTableTakenE.fnGetData(nTr);
    $("#modifyItem" + aData[0]).button();
    $("#deleteItem" + aData[0]).button();
    var divId = "#itemDetails" + aData[0];
    $("#modifyItem" + aData[0]).click(function() {
        $("#pop").dialog();
        $('#pop').on('dialogclose', function(event) {
            childTableTakenE.fnClose(nTr);
            $("#pop").remove();
        });
        (divId).empty();
        $(nTr).css("color", "#c5dbec");
    });
    $("#modSubmit").click(function() {
        nGrade = $("input[name=nGrade]").val();
        $('#nGrade').val(nGrade);
        sto_modCourse(childTableTakenE, divId, nTr, nGrade, nCID);
        childTableTakenE.fnUpdate([aData[0], aData[1], nGrade], nTr);
        $('#pop').dialog('close');
        childTableTakenE.fnClose(nTr);
        GetGPA();
    });
    $("#deleteItem" + aData[0]).click(function() {
        var del = confirm("Delete course?");
        if (del == true) {
            sto_deleteItem(childTableTakenE, divId, nTr);
            childTableTakenE.fnClose(nTr);
            childTableTakenE.fnDeleteRow(nTr);
            alert("Course Info for " + aData[0] + " deleted!");
            GetGPA();


        } else {
            courseTaken.fnClose(nTr);
        }
    });
}

function sto_openDetailsRow3(nTr) {
    studRoster.fnOpen(nTr, sto_formatStoreManagerDetailsAdmin(studRoster, nTr),
        "ui-state-highlight");
    var aData = studRoster.fnGetData(nTr);
    $("#modifyItem" + aData[0]).button();

    var divId = "#itemDetails" + aData[0];
    $("#modifyItem" + aData[0]).click(function() {

        adminLogin(aData[0]);

        $(location).attr('href', 'OvrlDash.html');
    });
    $("#deleteItem" + aData[0]).click(function() {
        var del = confirm("Delete course?");
        if (del == true) {
            sto_deleteItem2(divId, nTr);
        } else {
            studRoster.fnClose(nTr);
        }
    });
}

function adminLogin(adminUser) {
    var OvrlDashphpURL = 'OvrlDash.php';
    $.ajax({
        type: 'POST',
        url: OvrlDashphpURL,
        data: {
            type: '1',
            adminUser: adminUser
        },
        success: function(data) {},
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function sto_deleteItem2(divId, nTr) {
    // createLoadingDivAfter(divId,"Deleting Item");
    var aData = courseNeeded.fnGetData(nTr);
    var name = aData[1];
    var id = aData[0];
    $.ajax({
        type: 'POST',
        url: router,
        dataType: 'json',
        data: {
            action: 'deleteCourseNeeded',
            courseID: id
        },
        success: function(data) {
            //removeLoadingDivAfter(divId);
            if (data.success) {
                courseNeeded.fnClose(nTr);
                courseNeeded.fnDeleteRow(nTr);
            } else {
                $(nTr).css("color", "");
                courseNeeded.fnClose(nTr);
                alert("data.success = false");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function sto_openDetailsRow(nTr) {
    childTableDT.fnOpen(nTr, sto_formatStoreManagerDetails(childTableDT, nTr),
        "ui-state-highlight");
    var aData = childTableDT.fnGetData(nTr);
    $("#modifyItem" + aData[0]).button();
    $("#deleteItem" + aData[0]).button();
    var divId = "#itemDetails" + aData[0];
    $("#modifyItem" + aData[0]).click(function() {
        $("#pop").dialog();
        $('#pop').on('dialogclose', function(event) {
            childTableDT.fnClose(nTr);
            $("#pop").remove();
        });
        (divId).empty();
        $(nTr).css("color", "#c5dbec");
    });
    $("#modSubmit").click(function() {
        nGrade = $("input[name=nGrade]").val();
        $('#nGrade').val(nGrade);
        sto_modCourse(childTableDT, divId, nTr, nGrade, nCID);
        childTableDT.fnUpdate([aData[0], aData[1], nGrade], nTr);
        $('#pop').dialog('close');
        childTableDT.fnClose(nTr);
        GetGPA();
        //fnnn


    });
    $("#deleteItem" + aData[0]).click(function() {
        var del = confirm("Delete course?");
        if (del == true) {
            sto_deleteItem(courseTaken, divId, nTr);
            courseTaken.fnClose(nTr);
            courseTaken.fnDeleteRow(nTr);
            alert("Course Info for " + aData[0] + " deleted!");

            //fnnn

        } else {
            courseTaken.fnClose(nTr);
        }
    });
}

function sto_addItem(courseID, credits, grade, major) {
    //createLoadingDivAfter(containerId,"Creating Item");
    $.ajax({
        type: 'POST',
        url: router,
        dataType: 'json',
        data: {
            action: 'addCourse',
            courseID: courseID,
            credits: credits,
            grade: grade,
            major: major
        },
        success: function(data) {
            if (data.success) {
                courseTaken.fnAddData([
                    courseID,
                    credits,
                    grade
                ]);

            } else {
                alert("failed to add course");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });
}

function sto_modCourse(oTable, divId, nTr, nGrade, nCID) {
    // createLoadingDivAfter(divId,"Deleting Item");
    var aData = oTable.fnGetData(nTr);
    var major = aData[3];
    var grade = aData[2];
    var credits = aData[1];
    var id = aData[0];
    var newCourse = nCID;
    var newGrade = nGrade;
    $.ajax({
        type: 'POST',
        url: router,
        dataType: 'json',
        data: {
            action: 'modCourse',
            modifiedGrade: newGrade,
            modifiedCourse: newCourse,
            courseID: id,
            credits: credits,
            grade: grade,
            major: major
        },
        success: function(data) {
            //removeLoadingDivAfter(divId);
            if (data.success) {
                $(nTr).css("color", "");
                oTable.fnClose(nTr);
                //courseTaken.fnDeleteRow(nTr);
            } else {
                $(nTr).css("color", "");
                oTable.fnClose(nTr);
                alert("data.success = false");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function sto_modWeight(oTable, divId, nTr, nWeight, nRelev) {
    // createLoadingDivAfter(divId,"Deleting Item");
    var aData = oTable.fnGetData(nTr);
    var id = aData[0];
    var newRelev = nRelev;
    var newWeight = nWeight;
    $.ajax({
        type: 'POST',
        url: router,
        dataType: 'json',
        data: {
            action: 'modWeight',
            modifiedWeight: newWeight,
            modifiedRelevance: newRelev,
            courseID: id
        },
        success: function(data) {
            //removeLoadingDivAfter(divId);
            if (data.success) {
                $(nTr).css("color", "");
                oTable.fnClose(nTr);

            } else {
                $(nTr).css("color", "");
                oTable.fnClose(nTr);
                alert("data.success = false");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function sto_deleteItem(oTable, divId, nTr) {
    // createLoadingDivAfter(divId,"Deleting Item");
    var aData = oTable.fnGetData(nTr);
    var credits = aData[1];
    var id = aData[0];
    var grade = aData[2];

    $.ajax({
        type: 'POST',
        url: router,
        dataType: 'json',
        data: {
            action: 'deleteItem',
            courseID: id
        },
        success: function(data) {
            //removeLoadingDivAfter(divId);
            if (data.success) {
                oTable.fnClose(nTr);
                oTable.fnDeleteRow(nTr);

            } else {
                $(nTr).css("color", "");
                oTable.fnClose(nTr);
                alert("data.success = false");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function start2() {
    var graddata = new Array();
    var buckets = new Array();
    var admin = 1;

    $.ajax({
        type: 'POST',
        async: false,
        url: router,
        dataType: 'json',
        data: {
            action: 'editStudent'
        },
        success: function(data) {
            var sPath = window.location.pathname;
            var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);
            if(sPage == "student_roster.html"){
                if (data.message == "false"){
                    alert(data.why);
                    $(location).attr('href', 'OvrlDash.html');
                }
            }
            else{
                admin = 0;
                return;
            }
            studRoster = $('#studRost').dataTable({
                "aaData": data,
                "aaSorting": [
                    [0, "asc"]
                ],
                "aoColumns": [
                    //{ "bVisible": true},
                    {
                        "sTitle": "Username"
                    }, {
                        "sTitle": "Last name"
                    }, {
                        "sTitle": "First name"
                    }, {
                        "sTitle": "Email"
                    }
                ],
                "bJQueryUI": true,
                "bAutoWidth": true,
                "sPaginationType": "full_numbers"
            });
            $('#studRost').removeAttr("style");
            $('#studRost tbody tr td').off();
            $('#studRost tbody tr td').on('click',
                sto_rowClickHandler3);

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
    //alert(admin);
    if (admin == 1)
        return;

    $.ajax({
        type: 'POST',
        async: false,
        url: router,
        dataType: 'json',
        data: {
            action: 'getProgramInfo'
        },
        success: function (data) {
            $("#GPACalc").replaceWith('<p id = "GPACalc" style = "font-size:16px;">' + data[0] + '</p>');

            $('#studentMajData p').append(data[1]);
            $('#studentMajData p').replaceWith('<p>' + data[1] + '</p>');

            $("#studName").append(', ' + data[2]);

            graddata = data[3];
            for (var i = 0; i < data[3].length; i++) {
                var cs = data[3][i][0];
                $("#gradprogs").append('<option value ="' + cs +
                    '">' + cs + '</option>');
            }
            for (var i = 0; data[3].length; i++) {
                if ($("#gradprogs").val() == data[3][i][0]) {
                    $('#data p').append(data[3][i][1]);
                }
            }


        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });

    $.ajax({
        type: 'POST',
        url: router,
        dataType: 'json',
        data: {
            action: 'getMajorBuckets',
        },
        success: function(data) {
            buckets = data;
            courseTakenBuckets = $('#parentTableTaken').dataTable({
                "aaData": data,
                "aaSorting": [
                    [0, "asc"]
                ],
                "aoColumns": [
                    //{ "bVisible": true},
                    {
                        "sTitle": ""
                    }, {
                        "sTitle": "Courses Taken"
                    }, {
                        "sTitle": "All Required"
                    }
                ],
                order: [1, "asc"],
                columnDefs: [{
                    sortable: false,
                    targets: [0]
                }],
                "bJQueryUI": true,
                "fixedColumns": true,
                "retrieve": true,
                "iDisplayLength": 25
            });

            // $('#parentTableTaken').removeAttr("style");
            $('#parentTableTaken tbody tr td').off();
            $('#parentTableTaken tbody tr td').on('click',
                clickBucket);

            function clickBucket() {
                var nTr = this.parentNode;
                var open = false;
                try {
                    if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
                        open = true;
                } catch (err) {
                    alert(err);
                }
                if (open) {
                    /* This row is already open - close it */
                    courseTakenBuckets.fnClose(nTr);
                    $(nTr).css("color", "");
                } else {
                    //  openBucket(nTr,courseTakenBuckets);
                    var aData = courseTakenBuckets.fnGetData(nTr);
                    var bucket = aData[1];

                    $.ajax({
                        type: 'POST',
                        url: router,
                        dataType: 'json',
                        data: {
                            action: 'findChildBuckets',
                            bucket: bucket

                        },
                        success: function(data) {
                            if (data.success) {
                                //alert ("children found");

                                openChildBucketsTaken(nTr, courseTakenBuckets);
                            } else {
                                // alert ("no child buckets");
                                openBucket(nTr, courseTakenBuckets);
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("course doesnt exist");
                            alert(errorThrown);
                        }
                    });
                }
            }

            function openChildBucketsTaken(nTr, oTable) {
                var aData = oTable.fnGetData(nTr);
                var bucket = aData[1];

                var req = aData[2];

                oTable.fnOpen(nTr, sto_formatDataTableTakenChildBuckets(oTable,
                    nTr), "ui-state-highlight");
                var childTableDTTaken = null;

                $.ajax({
                    type: 'POST',
                    url: router,
                    dataType: 'json',
                    data: {
                        action: 'getMajorBucketsChildBuckets',
                        bucket: bucket
                    },
                    success: function(data) {
                        var bucketHTMLID = removeSpace(bucket);
                        //alert(bucketHTMLID);
                        childTableDTTaken = $('#childBucketsDTTaken' + bucketHTMLID).dataTable({
                            "aaData": data,
                            "aoColumns": [{
                                "sTitle": ""
                            }, {
                                "sTitle": "subbucket name"
                            }, {
                                "sTitle": "all required"
                            }, ],

                            "bAutoWidth": false,
                            "sPaginationType": "full_numbers",
                            "retrieve": true
                        });
                        $('#childBucketsDTTaken' + bucketHTMLID + ' tbody tr td').off();
                        $('#childBucketsDTTaken' + bucketHTMLID + ' tbody tr td').on('click', clickBucket2);


                        function clickBucket2() {
                            var nTr = this.parentNode;

                            var open = false;
                            try {
                                if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
                                    open = true;
                            } catch (err) {
                                alert(err);
                            }
                            if (open) {
                                /* This row is already open - close it */
                                childTableDTTaken.fnClose(nTr);
                                $(nTr).css("color", "");
                            } else {

                                var aData = childTableDTTaken.fnGetData(nTr);
                                var bucket = aData[1];
                                $.ajax({
                                    type: 'POST',
                                    url: router,
                                    dataType: 'json',
                                    data: {
                                        action: 'findChildBuckets',
                                        bucket: bucket

                                    },
                                    success: function(data) {
                                        if (data.success) {
                                            // alert ("children found");

                                            openChildBucketsTaken(nTr, childTableDTTaken);


                                        } else {
                                            // alert ("no child buckets");
                                            var req = aData[2];

                                            openBucket(nTr, childTableDTTaken);

                                        }
                                    },
                                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                                        alert("course doesnt exist");
                                        alert(errorThrown);
                                    }
                                });

                            }
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });
            }

            function openBucket(nTr, oTable) {

                oTable.fnOpen(nTr, sto_formatDataTable(oTable,
                    nTr), "ui-state-highlight");
                var aData = oTable.fnGetData(nTr);
                var bucket = aData[1];

                var childTableDT;
                $.ajax({
                    type: 'POST',
                    url: router,
                    dataType: 'json',
                    data: {
                        action: 'getMajorBucketsCourse',
                        bucket: bucket
                    },

                    success: function(data) {
                        var bucketHTMLID = removeSpace(bucket);
                        childTableDT = $('#coursesTakenDT' + bucketHTMLID).dataTable({
                            "aaData": data,
                            "aoColumns": [{
                                "sTitle": "Course ID"
                            }, {
                                "sTitle": "Credits"
                            }, {
                                "sTitle": "Grade"
                            }, ],

                            "bAutoWidth": false,
                            "sPaginationType": "full_numbers",
                            "retrieve": true,
                            "iDisplayLength": 25
                        });
                        $('#coursesTakenDT' + bucketHTMLID + ' tbody tr td').off();
                        $('#coursesTakenDT' + bucketHTMLID + ' tbody tr td').on('click',
                            sto_rowClickHandlerTaken);

                        function sto_rowClickHandlerTaken() {
                            var nTr = this.parentNode;
                            var open = false;
                            try {
                                if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
                                    open = true;
                            } catch (err) {
                                alert(err);
                            }
                            if (open) {
                                /* This row is already open - close it */
                                childTableDT.fnClose(nTr);
                                $(nTr).css("color", "");
                            } else {
                                sto_openDetailsRowTaken(nTr);
                            }
                        }

                        function sto_openDetailsRowTaken(nTr) {
                            childTableDT.fnOpen(nTr, sto_formatStoreManagerDetails(childTableDT, nTr),
                                "ui-state-highlight");
                            var aData = childTableDT.fnGetData(nTr);
                            $("#modifyItem" + aData[0]).button();
                            $("#deleteItem" + aData[0]).button();
                            var divId = "#itemDetails" + aData[0];

                            $("#modifyItem" + aData[0]).click(function() {
                                $("#pop").dialog();
                                $('#pop').on('dialogclose', function(event) {
                                    childTableDT.fnClose(nTr);
                                    $("#pop").remove();
                                });
                                (divId).empty();
                                $(nTr).css("color", "#c5dbec");
                            });
                            $("#modSubmit").click(function() {
                                nGrade = $("input[name=nGrade]").val();
                                $('#nGrade').val(nGrade);
                                sto_modCourse(childTableDT, divId, nTr, nGrade, nCID);
                                childTableDT.fnUpdate([aData[0], aData[1], nGrade], nTr);
                                $('#pop').dialog('close');
                                childTableDT.fnClose(nTr);
                                GetGPA();
                            });
                            $("#deleteItem" + aData[0]).click(function() {
                                var del = confirm("Delete course?");
                                if (del == true) {
                                    sto_deleteItem(childTableDT, divId, nTr);
                                    childTableDT.fnClose(nTr);
                                    childTableDT.fnDeleteRow(nTr);
                                    alert("Course Info for " + aData[0] + " deleted!");
                                    GetGPA();
                                    RequirementMet(bucket);

                                } else {
                                    childTableDT.fnClose(nTr);
                                }

                            });

                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });
            }

            displayNeeded();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });

    function displayNeeded() {
        courseNeededBuckets = $('#parentTable').dataTable({
            "aaData": buckets,
            "aaSorting": [
                [0, "asc"]
            ],
            "aoColumns": [
                //{ "bVisible": true},
                {
                    "sTitle": ""
                }, {
                    "sTitle": "Courses Needed"
                }, {
                    "sTitle": "All Required"
                },
            ],
            order: [1, "asc"],
            columnDefs: [{
                sortable: false,
                targets: [0]
            }],
            "bJQueryUI": true,
            "retrieve": true,
            "iDisplayLength": 25
        });

        // $('#parentTableTaken').removeAttr("style");
        $('#parentTable tbody tr td').off();
        $('#parentTable tbody tr td').on('click',
            clickBucket);

        $("#generateForecast").click(function() {

            setTimeout(function() {
                GenerateForecast();

            }, 1000);

        });

        function clickBucket() {
            var nTr = this.parentNode;

            var open = false;
            try {
                if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
                    open = true;
            } catch (err) {
                alert(err);
            }
            if (open) {
                /* This row is already open - close it */
                courseNeededBuckets.fnClose(nTr);
                $(nTr).css("color", "");
            } else {
                var aData = courseNeededBuckets.fnGetData(nTr);
                var bucket = aData[1];

                $.ajax({
                    type: 'POST',
                    url: router,
                    dataType: 'json',
                    data: {
                        action: 'findChildBuckets',
                        bucket: bucket

                    },
                    success: function(data) {
                        if (data.success) {
                            // alert ("children found");
                            openChildBuckets(nTr, courseNeededBuckets);


                        } else {
                            //alert ("no child buckets");
                            openBucket(nTr, courseNeededBuckets);
                            /*var req = aData[2];
                            if (req == "YES") {
                                openBucket(nTr, courseNeededBuckets);
                            } else {
                                openBucket2(nTr, courseNeededBuckets);
                            }*/
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("course doesnt exist");
                        alert(errorThrown);
                    }
                });

            }
        }

        function openChildBuckets(nTr, oTable) {
            var aData = oTable.fnGetData(nTr);
            var bucket = aData[1];

            var req = aData[2];

            oTable.fnOpen(nTr, sto_formatDataTableNeededChildBuckets(oTable,
                nTr), "ui-state-highlight");
            var childTableDTNeeded2 = null;

            $.ajax({
                type: 'POST',
                url: router,
                dataType: 'json',
                data: {
                    action: 'getMajorBucketsChildBuckets',
                    bucket: bucket
                },
                success: function(data) {
                    var bucketHTMLID = removeSpace(bucket);
                    //alert(bucketHTMLID);
                    childTableDTNeeded2 = $('#childBucketsDT' + bucketHTMLID).dataTable({
                        "aaData": data,
                        "aoColumns": [{
                            "sTitle": ""
                        }, {
                            "sTitle": "subbucket name"
                        }, {
                            "sTitle": "all required"
                        }, ],

                        "bAutoWidth": false,
                        "sPaginationType": "full_numbers",
                        "retrieve": true
                    });
                    $('#childBucketsDT' + bucketHTMLID + ' tbody tr td').off();
                    $('#childBucketsDT' + bucketHTMLID + ' tbody tr td').on('click', clickBucket2);

                    //RequirementMet(bucket);

                    function clickBucket2() {
                        var nTr = this.parentNode;

                        var open = false;
                        try {
                            if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
                                open = true;
                        } catch (err) {
                            alert(err);
                        }
                        if (open) {
                            /* This row is already open - close it */
                            childTableDTNeeded2.fnClose(nTr);
                            $(nTr).css("color", "");
                        } else {

                            var aData = childTableDTNeeded2.fnGetData(nTr);
                            var bucket = aData[1];

                            $.ajax({
                                type: 'POST',
                                url: router,
                                dataType: 'json',
                                data: {
                                    action: 'findChildBuckets',
                                    bucket: bucket

                                },
                                success: function(data) {
                                    if (data.success) {
                                        // alert ("children found");

                                        openChildBuckets(nTr, childTableDTNeeded2);
                                    } else {
                                        // alert ("no child buckets");
                                        var req = aData[2];
                                        if (req == "YES") {
                                            openBucket(nTr, childTableDTNeeded2);
                                        } else {
                                            openBucket2(nTr, childTableDTNeeded2);
                                        }
                                    }


                                },
                                error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    alert("course doesnt exist");
                                    alert(errorThrown);
                                }
                            });

                        }
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });
        }

        function openBucket(nTr, oTable) {
            var aData = oTable.fnGetData(nTr);
            var bucket = aData[1];
            var req = aData[2];

            oTable.fnOpen(nTr, sto_formatDataTableNeeded2(oTable,
                nTr), "ui-state-highlight");

            var childTableDTNeeded;

            $.ajax({
                type: 'POST',
                url: router,
                dataType: 'json',
                data: {
                    action: 'getMajorBucketsCourseNeeded',
                    bucket: bucket
                },
                success: function(data) {
                    var bucketHTMLID = removeSpace(bucket);
                    childTableDTNeeded = $('#coursesNeededDT' + bucketHTMLID).dataTable({
                        "aaData": data,
                        "aoColumns": [{
                            "sTitle": "Course ID"
                        }, {
                            "sTitle": "Credits"
                        }, {
                            "sTitle": "Weight"
                        }, {
                            "sTitle": "Relevance"
                        }, ],

                        "bAutoWidth": false,
                        "sPaginationType": "full_numbers",
                        "retrieve": true,
                        "iDisplayLength": 25
                    });
                    $('#coursesNeededDT' + bucketHTMLID + ' tbody tr td').off();
                    $('#coursesNeededDT' + bucketHTMLID + ' tbody tr td').on('click',
                        sto_rowClickHandlerNeeded);

                    function sto_rowClickHandlerNeeded() {
                        var nTr = this.parentNode;
                        var open = false;
                        try {
                            if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
                                open = true;
                        } catch (err) {
                            alert(err);
                        }
                        if (open) {
                            /* This row is already open - close it */
                            childTableDTNeeded.fnClose(nTr);
                            $(nTr).css("color", "");
                        } else {
                            sto_openDetailsRowNeeded(nTr);
                        }
                    }

                    function sto_openDetailsRowNeeded(nTr) {
                        childTableDTNeeded.fnOpen(nTr, sto_formatStoreManagerDetails2(childTableDTNeeded, nTr),
                            "ui-state-highlight");
                        var aData = childTableDTNeeded.fnGetData(nTr);
                        $("#modifyItem" + aData[0]).button();
                        $("#moveItem" + aData[0]).button();
                        var divId = "#itemDetails" + aData[0];
                        $("#modifyItem" + aData[0]).click(function() {
                            $("#pop2").dialog();
                            $('#pop2').on('dialogclose', function(event) {
                                childTableDTNeeded.fnClose(nTr);

                                $(nTr).css("color", "#c5dbec");
                                $("#pop2").remove();
                            });(divId).empty();
                        });
                        $("#modSubmit2").click(function() {
                            nRelev = $("input[name=nRelev]").val();
                            nWeight = $("input[name=nWeight]").val();
                            sto_modWeight(childTableDTNeeded, divId, nTr, nWeight, nRelev);
                            childTableDTNeeded.fnUpdate([aData[0], aData[1], nWeight, nRelev],
                                nTr);
                            $('#pop2').dialog('close');
                        });

                        $("#moveItem" + aData[0]).click(function() {
                            $(nTr).css("color", "#c5dbec");
                            addArrow(childTableDTNeeded, nTr);
                        });
                    }

                    if (data.length == 0)
                    {
                        var change = bucket;
                        if ($("td:contains('" + change + "')").text().indexOf("Requirement") == -1) {

                            var elem = $("td:contains('" + change + "')");

                            elem.append('<p style = "font-size:9px;color:blue;" class = "appendReq' + bucket + '"> Requirement met</p>');
                        }
                    }

                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(errorThrown);
                }

            });
        }

        function openBucket2(nTr, oTable) {
            var aData = oTable.fnGetData(nTr);
            var bucket = aData[1];
            var req = aData[2];

            if (bucket != "Natural Sciences - Group 1" && bucket != "Natural Sciences - Group 2") {
                oTable.fnOpen(nTr, sto_formatDataTableNeeded2(oTable,
                    nTr), "ui-state-highlight");
            } else {
                oTable.fnOpen(nTr, sto_formatDataTableNaturalScience(oTable,
                    nTr), "ui-state-highlight");
            }
            var childTableDTNeededCourses;

            $("#addECButton").click(function() {

                var course = $("input[name=courseAdded]").val();
                //alert("course added: " + course + bucket);
                var sure = confirm("Please check with your advisor before continuing. Does the course entered meet the criteria for the category requirement?");
                if (sure)
                    AddExtraCourse(course, bucket);

            });

/*            function GetCourseReqs(bucket) {
                $.ajax({
                    type: 'POST',
                    url: OvrlDashphpURL,
                    dataType: 'json',
                    data: {
                        action: 'getMinReq',
                        bucket: bucket
                    },
                    success: function(data) {
                        var change = bucket;
                        var changes = JSON.stringify(bucket);
                        //	alert("selected: "+data[0][0] + " req: "+data[0][1]);
                        if (data[0][0] >= data[0][1]) {
                            //if (append ==false){
                            if ($("td:contains('" + change + "')").text().length / 2 == bucket.length) {

                                var elem = $("td:contains('" + change + "')");
                                //
                                elem.append('<p style = "font-size:9px;color:blue;" class = "appendReq' + bucket + '"> Requirement met</p>');
                                append = true;
                                //alert("requirement met Req: " + data[0][0] + " selectd: " + data[0][1]   );
                            }
                        } else {
                            //alert("requirement not met");
                            $("td:contains('" + change + "')").children().remove();
                            //('<p>').attr('class','appendReq' + bucket).remove
                            append = false;
                            //alert("requirement not met");
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });
            }

            function AddExtraCourse(courseID, bucket) {
                $.ajax({
                    type: 'POST',
                    url: OvrlDashphpURL,
                    dataType: 'json',
                    data: {
                        action: 'addExtraCourse',
                        bucket: bucket,
                        courseID: courseID
                    },
                    success: function(data) {
                        if (data.success) {
                            alert("course added");
                        } else {
                            alert("course doesnt exist ifdata");
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("course doesnt exist");
                        alert(errorThrown);
                    }
                });
            }*/

            var childTableDTNeededCourses2;
            $.ajax({
                type: 'POST',
                url: router,
                dataType: 'json',
                data: {
                    action: 'getMajorBucketsCourseNeeded',
                    bucket: bucket
                },
                success: function(data) {
                    //	alert(JSON.stringify(data)+ ": openbucket2data");
                    var bucketHTMLID = removeSpace(bucket);
                    childTableDTNeededCourses2 = $('#coursesNeededDT' + bucketHTMLID).dataTable({
                        "aaData": data,
                        "aoColumns": [{
                            "sTitle": "Course ID"
                        }, {
                            "sTitle": "Credits"
                        }, {
                            "sTitle": "Weight"
                        }, {
                            "sTitle": "Relevance"
                        }, {
                            "sTitle": "Select"
                        }, ],

                        "bAutoWidth": false,
                        "sPaginationType": "full_numbers",
                        "retrieve": true,
                        "iDisplayLength": 25
                    });

                    $('#coursesNeededDT' + bucketHTMLID + ' tbody tr td:nth-child(-n+4)').off();
                    $('#coursesNeededDT' + bucketHTMLID + ' tbody tr td:nth-child(-n+4)').on('click',
                        sto_rowClickHandlerNeeded);
                   /* $('#coursesNeededDT' + bucketHTMLID + ' tbody tr td:nth-child(5)').off();
                    $('#coursesNeededDT' + bucketHTMLID + ' tbody tr td:nth-child(5)').on('click',
                        CheckboxChange);*/

                    if (data.length == 0)
                    {
                        var change = bucket;
                        if ($("td:contains('" + change + "')").text().indexOf("Requirement") == -1) {

                            var elem = $("td:contains('" + change + "')");

                            elem.append('<p style = "font-size:9px;color:blue;" class = "appendReq' + bucket + '"> Requirement met</p>');
                        }
                    }


/*                    function CheckboxChange() {



                        var nTr = this.parentNode;
                        var aData = childTableDTNeededCourses2.fnGetData(nTr);
                        var courseID = aData[0];
                        var state;
                        if ($('#' + courseID + 'check').prop('checked'))

                        {
                            //alert("checked");
                            document.getElementById(courseID + "check").checked = false;
                            state = 0;
                            ChangeCheckboxAjax(state, courseID);
                        } else {
                            // alert("not chcked");
                            document.getElementById(courseID + "check").checked = true;
                            state = 1;
                            ChangeCheckboxAjax(state, courseID);
                        }
                    }

                    function ChangeCheckboxAjax(state, courseID) {

                        var OvrlDashphpURL = 'OvrlDash.php';
                        $.ajax({
                            type: 'POST',
                            url: OvrlDashphpURL,
                            dataType: 'json',
                            data: {
                                action: 'changeSelected',
                                state: state,
                                courseID: courseID
                            },
                            success: function(data) {

                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert(errorThrown);
                            }
                        });
                        RequirementMet(bucket);

                    }*/

                    function sto_rowClickHandlerNeeded() {
                        var nTr = this.parentNode;
                        var open = false;

                        try {
                            if ($(nTr).next().children().first().hasClass("ui-state-highlight"))
                                open = true;
                        } catch (err) {
                            alert(err);
                        }
                        if (open) {
                            /!* This row is already open - close it *!/
                            childTableDTNeededCourses2.fnClose(nTr);
                            $(nTr).css("color", "");
                        } else {
                            sto_openDetailsRowNeeded(nTr);
                        }
                    }

                    function sto_openDetailsRowNeeded(nTr) {
                        childTableDTNeededCourses2.fnOpen(nTr, sto_formatStoreManagerDetails2(childTableDTNeededCourses2, nTr),
                            "ui-state-highlight");
                        var aData = childTableDTNeededCourses2.fnGetData(nTr);
                        $("#modifyItem" + aData[0]).button();
                        $("#moveItem" + aData[0]).button();
                        var divId = "#itemDetails" + aData[0];
                        $("#modifyItem" + aData[0]).click(function() {
                            $("#pop2").dialog();
                            $('#pop2').on('dialogclose', function(event) {
                                childTableDTNeededCourses2.fnClose(nTr);

                                $(nTr).css("color", "#c5dbec");
                                $("#pop2").remove();
                            });(divId).empty();
                        });
                        $("#modSubmit2").click(function() {
                            nRelev = $("input[name=nRelev]").val();
                            nWeight = $("input[name=nWeight]").val();
                            sto_modWeight(childTableDTNeededCourses2, divId, nTr, nWeight, nRelev);
                            childTableDTNeededCourses2.fnUpdate([aData[0], aData[1], nWeight, nRelev, aData[4]],
                                nTr);
                            $('#pop2').dialog('close');
                        });

                        $("#moveItem" + aData[0]).click(function() {
                            $(nTr).css("color", "#c5dbec");
                            addArrow(childTableDTNeededCourses2, nTr);
                        });
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(errorThrown);
                }

            });
        }
    }

    $("#myonoffswitch").click(function() {
        $("#coursesTaken tbody td:nth-child(3) ").toggle();
        // var column = $('#coursesTaken').dataTable().api().column( 13 ).visible( false );
        //var oTable = $('#coursesTaken').dataTable();
        //  var bVis = oTable.fnSettings().aoColumns[22].bVisible;
        //  oTable.fnSetColumnVis( 2, bVis ? false : true );
        // Toggle the visibility
        //  column.visible( ! column.visible() );
        $(".GPACalcBox p:nth-child(2)").toggle();
    });

    function fnAddCourseTaken() {
        var course = $("input[name=courseTaken1]").val();
        var credits = $("input[name=courseCredits1]").val();
        var grade = $("input[name=Grade]").val();
        var major = $("input[name=major]").val();
        sto_addItem(course, credits, grade, major);
        $("tr:contains('" + course + "')").each(function() {
            courseNeeded.fnDeleteRow(this);
            //fnnn
        });
    }

    $("#gradprogs").change(function() {
        for (var i = 0; i < graddata.length; i++) {
            if ($("#gradprogs").val() == graddata[i][0]) {
                $("#data p:first").replaceWith('<p>' + graddata[i]
                        [1] + '</p>');
                var reqGrdtext = $('#data p:first').text();
                var curGPAtext = $('#GPACalc').text();
                var curGPA = parseFloat(curGPAtext);
                var reqGrd = parseFloat(reqGrdtext);
            }
        }
    });

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

                if(data != 'No data for graph') {

                    $('#current_course').append('<br><h2 id="gtitle">GPA History</h2>');
                    $('#current_course').append('<div id="placeholder"></div>');

                    var ticks = []; //split data for the x-axis label
                    var avg = [];
                    for (var i = 0; i < data.length; i++) {
                        ticks.push([i, data[i][0]]);
                        avg.push([i, data[i][1]]);
                    }

                    var data2 = [avg]; //data to plot

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
                    $('#current_course').append('<div id="graph_legend"><br><ul class="legend"><li>' +
                        '<span class="course0"></span>GPA Average at FIU</li></ul><br></div>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
}

