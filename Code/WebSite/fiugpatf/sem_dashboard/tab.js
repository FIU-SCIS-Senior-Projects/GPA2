var course;
var maintab;
var gradeTable;
var router = 'semesterDashboardRouter.php';

$(document).ready(function(){
    course = getUrlVars()["id"];
    getCourse();
    //makeTabs();
});

function getCourse()
{
    $.ajax({
        type: 'POST',
        url: router,
        data: {
            action: 'tabs',
            course: course
        },
        dataType: 'json',
        success: function(data) {
            var tabs = new Array();
            for(var i = 0; i < data.length; i++)
            {
                tabs.push(data[i]);
            }
            makeTabs(tabs)
        }
    });
}

function makeTabs(tabs)
{
    $('#tabs').html(""); //clear div
    $('#tabs').append("<ul></ul>");
    var div;
    var div = '';
    div += '<div id="addTab">';
    div += '         <table cellpadding="0" cellspacing="0" border="0" class="display" id="allAssess"></table>';
    div += '         <button type="button" id="addBreak" class="tabButton">Add Assessment</button>';
    div += '         <div id="empty"><h1 id="gtitle">Grade Trends for ' + course +'</h1>'
    div += '         <div id="placeholder"></div></div>';
    div += '</div>';
    $('#tabs').append(div);
    $('#tabs ul').append('<li><a href="#addTab">Assessment Managment</a></li>');


	$.ajax({
		type: 'POST',
		url: router,
		data: {
		    action: 'getAllAssessments',
	        course: course
	    },
		dataType: 'json',
		success: function(data) {
		    gradeTable = $('#allAssess').dataTable({
		        "aaData": data,
		        "aoColumns": [
		            {"sTitle": "Assessment"},
		            {"sTitle": "Percentage"},
		            {"sTitle": "Cumulative Average"}
		        ],
		        "bJQueryUI": true,
		        "bAutoWidth": false,
		        "sPaginationType": "full_numbers",
				retrieve: true
		    });
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
		    alert(errorThrown);
		}
	});
	$.ajax({
		type: 'POST',
		url:router,
		data: {
		    action: 'plotPoints',
	        course: course
	    },
		dataType: 'json',
		success: function(data) {
		    $.plot($('#placeholder'), [data.slice(0, data.length - 1)], {
		    	xaxis:{
		    		axisLabel: "Date",
		    		ticks: data[data.length - 1]
		    	},
		    	yaxis:{
		    		max: 100,
		    		axisLabel: "Running Grade"
		    	},
		    	series:{
		    		points: {
		    			radius: 3
		    		}
		    	}
		    });
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
		    alert(errorThrown);
		}
	});

    for(var i = 0; i<tabs.length; i++)
    {
        $('#tabs ul').append('<li><a href="#' + removeSpace(tabs[i]) + '"> ' + tabs[i] + '</a></li>');
        createDiv(tabs[i]);
    }

	//$('#tabs ul').append('<li><a href="#help">Help</a></li>');
	//createHelp();


    $('#addBreak').click(function(){
        addAssesment();
    })

    maintab = $('#tabs').tabs();

    maintab.find( ".ui-tabs-nav" ).sortable({
        axis: "x",
        stop: function() {
            tabs.tabs( "refresh" );
        }
    });
}

function createDiv(assessment)
{
	var assessmentNoSpace = removeSpace(assessment);
    var div = '';
    div += '<div id="'+ assessmentNoSpace +'">';
    div += '<table cellpadding="0" cellspacing="0" border="0" class="display" id="' + assessmentNoSpace +'grades"></table>';
    div +=     '<form>';
    div +=         '<button type="button" id="button' + assessmentNoSpace +'" class="tabButton">Add Grade</button><br>';
    div += '        <button type="button" id="remove' + assessmentNoSpace +'" class="tabButton">Remove Assessment</button><br>';
    div +=     '</form>';
    div += '</div>';

    $('#tabs').append(div);
    var currCourse;
    $.ajax({
        type: 'POST',
        url: router,
        data: {
            action: 'getGrades',
            assessment: assessment,
            course: course
        },
        dataType: 'json',
        success: function(data) {
            currCourse = $('#' + assessmentNoSpace + 'grades').dataTable({
                "aaData": data,
                "aoColumns": [
                    {"sTitle": ""},
                    {"sTitle": "Grade"}
                ],
                "bJQueryUI": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers"
            });
            $('#' + assessmentNoSpace + 'grades ' + 'tbody tr td').off();
            $('#' + assessmentNoSpace + 'grades ' + 'tbody tr td').on('click', curr_rowClickHandler);

            function curr_rowClickHandler()
            {
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
                $("#" + assessmentNoSpace + "Remove"+aData[0]).button();
                $("#" + assessmentNoSpace + "Modify"+aData[0]).button();

                var divId = "#" + assessmentNoSpace + "Details"+aData[0];

                $("#" + assessmentNoSpace + "Remove"+aData[0]).click(function(){
                    remove_assessment(aData[1], nTr);
                });

                $("#" + assessmentNoSpace + "Modify"+aData[0]).click(function(){
                    modify_assessment(aData[1],aData[0], nTr);
                });
            }

            function curr_formatStoreManagerDetails ( oTable, nTr )
            {
                var aData = oTable.fnGetData( nTr );
                var id = aData[0];
                var sOut = '';
                sOut += '<div id="' + assessmentNoSpace +'Details'+id+'">';
                sOut += '    <div class="buttonColumnDetails">';
                sOut += '        <button class="breakdownButton" id="' + assessmentNoSpace + 'Remove'+id+'">Remove</button><br>';
                sOut += '        <button class="breakdownButton" id="' + assessmentNoSpace + 'Modify'+id+'">Modify</button><br>';
                sOut += '    </div>';
                sOut += '</div>';
                return sOut;

            }

            function remove_assessment(grade, nTr)
            {
                var del = confirm("Delete grade?");
                if (del == true) {
                    $.ajax({
                        type: 'POST',
                        url: router,
                        data: {
                            action: 'removeGrade',
                            assessment: assessment,
                            grade: grade,
                            course: course
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data[0] == 'success') {
                                currCourse.fnClose(nTr);
                                currCourse.fnDeleteRow(nTr);
								recreateGradeTable();
                            }
                            else {
                                alert("Error: Course was not removed.");
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown){
                            alert(errorThrown);
                        }
                    });
                }
            }

            function modify_assessment(grade, id, nTr)
            {
                var row = parseInt(id.slice(-1)) - 1;

                var div = '';
                div += '<div id="Modify'+ id +'" title="Modify Grade">';
                div +=     '<form>';
                div +=         '<h1>Modify Grade</h1>';
                div +=         '<input type="text" id="grade'+ id + '"><br>';
                div +=         '<button type="button" id="button' + id +'">Modify Grade</button><br>';
                div +=     '</form>';
                div += '</div>';

                $('#' + assessmentNoSpace).append(div);

                $('#Modify' + id).dialog({
                    modal: true
                });

                $("#button" + id).click(function(){
                    var newGrade = $('#grade' + id).val();

                    $.ajax({
                        type: 'POST',
                        url: router,
                        data: {
                            action: 'modifyGrade',
                            course: course,
                            assessment: assessment,
                            grade: grade,
                            newGrade: newGrade
                        },
                        dataType: 'json',
                        success: function(data) {
                            if(data[0] == "success")
                            {
                                currCourse.fnUpdate(newGrade, row, 1);
								recreateGradeTable();
								currCourse.fnClose( nTr );
                            }
							else
							{
								alert("Grade Not Modified\nError: " + data);
							}
                        }
                    });
                    $('#Modify' + id).dialog('close');
                    $('#Modify'+ id).remove();
                });
            }

			function addGrade(assessment,  currCourse)
			{
				var assessmentNoSpace = removeSpace(assessment);
				var div = '';
				div +='<div id="AddGradeDialog" title="Add Grade">';
				div +='    <h1>Add Grade</h1>';
				div +='    <input type="text" id="grade'+ assessmentNoSpace + '">';
				div +='    <button id="AddGradeSub">Submit</button>';
				div +='</div>';
	
				$('#' + assessmentNoSpace).append(div);

				$('#AddGradeSub').click(function(){
					var grade = $('#grade' + assessmentNoSpace).val();
					$.ajax({
						type: 'POST',
						url: router,
						data: {
						    action: 'addGrade',
						    course: course,
						    assesment: assessment,
						    grade: grade
						},
						dataType: 'json',
						success: function(data) {
						    if(data[0] == "success")
						    {
						        currCourse.dataTable().fnAddData([
						            "Grade" + (currCourse.fnSettings().fnRecordsTotal() + 1),
						            grade
						        ]);
								recreateGradeTable();
								$('#' + assessmentNoSpace + 'grades ' + 'tbody tr td').off();
					            $('#' + assessmentNoSpace + 'grades ' + 'tbody tr td').on('click', curr_rowClickHandler);
						        $('#AddGradeDialog').dialog("close");
						    }
							else
							{
								alert("Grade Not Added\nError: " + data);
							}
						}
					});
				});

				$('#AddGradeDialog').dialog({
					modal: true,
					close: function( event, ui ) {
						$('#AddGradeDialog').remove();
					}
				});
			}
			$('#button' + assessmentNoSpace).click(function(){
        		addGrade(assessment, currCourse);
    		});

			$('#' + 'remove' + assessmentNoSpace).click(function(){
				removeAssessment(assessment);
			});
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        }
    });

/*
    $('#button' + assessmentNoSpace).click(function(){
        addGrade(assessment, currCourse);
    });

    $('#' + 'remove' + assessmentNoSpace).click(function(){
        removeAssessment(assessment);
    });
*/
}

function removeAssessment(assessment)
{
	var assessmentNoSpace = removeSpace(assessment);
    $.ajax({
        type: 'POST',
        url: router,
        data: {
            action: 'removeBucket',
            course: course,
            assessment: assessment
        },
        dataType: 'json',
        success: function(data) {
            if(data[0] == "success")
            {
                $("#" + assessmentNoSpace).remove();
                $("li[aria-controls = '" + assessmentNoSpace +"']").remove();
                maintab.tabs("refresh");
				recreateGradeTable();
            }
			else
			{
				alert("Assessment Not Modified\nError: " + data);
			}
        }
    });
}

/*
function addGrade(assessment,  currCourse)
{
	var assessmentNoSpace = removeSpace(assessment);
    var div = '';
    div +='<div id="AddGradeDialog" title="Add Grade">';
    div +='    <h1>Add Grade</h1>';
    div +='    <input type="text" id="grade'+ assessmentNoSpace + '">';
    div +='    <button id="AddGradeSub">Submit</button>';
    div +='</div>';
	
    $('#' + assessmentNoSpace).append(div);

    $('#AddGradeSub').click(function(){
        var grade = $('#grade' + assessmentNoSpace).val();
        $.ajax({
            type: 'POST',
            url: 'tabs.php',
            data: {
                action: 'addGrade',
                course: course,
                assesment: assessment,
                grade: grade
            },
            dataType: 'text',
            success: function(data) {
                if(data == "true")
                {
                    currCourse.dataTable().fnAddData([
                        "Grade" + (currCourse.fnSettings().fnRecordsTotal() + 1),
                        grade
                    ]);
					recreateGradeTable();
                    $('#AddGradeDialog').dialog("close");
                }
				else
				{
					alert("Grade Not Added\nError: " + data);
				}
            }
        });
    });

    $('#AddGradeDialog').dialog({
        modal: true,
        close: function( event, ui ) {
            $('#AddGradeDialog').remove();
        }
    });
}
*/

function addAssesment()
{
    var div = '';
    div += '     <div id="AssessmentAddition" title="Add Assessment">';
	div += '         <table>';
	div += '             <thead>';
	div += '                 <tr>';
    div += '                     <td>Assessment Type:</td>' ;
	div += '                     <td><input type="text" id="assesment"></td>';
	div += '                 </tr>';
	div += '                 <tr>';
	div += '                     <td>Percentage:</td>';
	div += '                     <td><input type="text" id="percentage"></td>';
	div += '                 </tr>';
	div += '             </thead>';
	div += '         </table>';
    div += '         <button type="button" id="subAsses">Submit</button>';
    $('#addTab').append(div);

    $('#subAsses').click(function(){
        var assessment = $('#assesment').val();
		var assessmentNoSpace = removeSpace(assessment);
        var percentage = $('#percentage').val();
        $.ajax({
            type: 'POST',
            url: router,
            data: {
                action: 'add',
                course: course,
                assessment: assessment,
                percentage: percentage
            },
            dataType: 'json',
            success: function(data) {
                if(data[0] == 'success')
                {
                    $('#tabs ul').append('<li><a href="#' + assessmentNoSpace + '"> ' + assessment + '</a></li>');
                    createDiv(assessment);
                    maintab.tabs("refresh");
                    $('#AssessmentAddition').dialog("close");
					gradeTable.fnAddData([
                        assessment,
                        percentage,
                        "No Grades"
                    ]);
                }
				else
				{
					alert("Assessment Not Added\nError: " + data);
				}
            }
        });
    });

    $('#AssessmentAddition').dialog({
        modal: true,
        width: "500px",
        close: function( event, ui ) {
            $('#AssessmentAddition').remove();
        }
    });
}

function recreateGradeTable()
{
	gradeTable.fnDestroy();

	$.ajax({
		type: 'POST',
		url: router,
		data: {
		    action: 'getAllAssessments',
	        course: course
	    },
		dataType: 'json',
		success: function(data) {
		    gradeTable = $('#allAssess').dataTable({
		        "aaData": data,
		        "aoColumns": [
		            {"sTitle": "Assessment"},
		            {"sTitle": "Percentage"},
		            {"sTitle": "Cumulative Average"}
		        ],
		        "bJQueryUI": true,
		        "bAutoWidth": false,
		        "sPaginationType": "full_numbers",
				retrieve: true
		    });
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
		    alert(errorThrown);
		}
	});
}

function removeSpace(string)
{
	var substrings = string.split(" ");
	string = "";
	for(var i = 0; i < substrings.length; i++)
	{
		string = string.concat(substrings[i]);
	}
	return string;
}

function getUrlVars() {
    var map = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        map[key] = value;
    });
    return map;
}
