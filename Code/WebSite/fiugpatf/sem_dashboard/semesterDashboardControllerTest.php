<?php
/**
 * Created by PhpStorm.
 * User: Lizette Mendoza
 * Date: 3/11/16
 * Time: 3:54 PM
 */

include_once '../common_files/dbconnector.php';
include_once 'semesterDashboardController.php';

class SemesterDashboardControllerTest extends PHPUnit_Framework_TestCase {

    /*function testCurrentAssessments001_UT001() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->currentAssessments();

        $expected = [{"id":"COP4555","name":"Principles of Programming Languages","credits":3,"grade":82.72},{"id":"MAD3512"
,"name":"Introduction to Theory of Algorithm","credits":3,"grade":"No Grades"},{"id":"CIS4911","name"
:"Senior Project","credits":3,"grade":85},{"id":"COP4610","name":"Operating Syst Princ","credits":3,"grade"
:86.4}];

        $this->assertEquals($return, $expected);
    }*/

    function testCurrentAssessments002_UT014() {

        $sdc = new SemesterDashboardController(27, 'lmend066');
        $return = $sdc->currentAssessments();

        //expected output for unit test
        $expected = [];

        $this->assertEquals($return, $expected);
    }

    function testGetGraphData001_UT002() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->getGraphData();

        //expected output for unit test
        $expected = [
            ["01-11","02-01","02-22","03-14"],
            [
                [100,84.5,89.733333333333,86.244444444444],
                [100,93,90.5,82.723],
                [100,100,85,85]
            ]
        ];

        $this->assertEquals($return, $expected);
    }

    function testCourseLegend001_UT003() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->courseLegend();

        //expected output for unit test
        $expected = ["COP4555","COP4610","CIS4911"];

        $this->assertEquals($return, $expected);
    }

    function testGetGradProgram001_UT004() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->getGradProgram();

        //expected output for unit test
        $expected = [
            ["Masters in IT",3.2],
            ["Masters in Mathematics",2.8],
            ["Masters in Computer Science",3]
        ];

        $this->assertEquals($return, $expected);
    }

    function testGetCurrentProgram001_UT005() {
        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->getCurrentProgram();

        //expected output for unit test
        $expected = [
            ["Computer Science"]
        ];

        $this->assertEquals($return, $expected);
    }

    function testGetCurrentProgram002_UT015() {
        $sdc = new SemesterDashboardController(27, 'lmend066');
        $return = $sdc->getCurrentProgram();

        //expected output for unit test
        $expected = [];

        $this->assertEquals($return, $expected);
    }

    function testTabs001_UT006() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->tabs('CIS4911');

        //expected output for unit test
        $expected = ["ESSAYS"];

        $this->assertEquals($return, $expected);

    }

    function testTabs002_UT010() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->tabs('COP4610');

        //expected output for unit test
        $expected = ["Final","Homework","Midterm","Quizes"];

        $this->assertEquals($return, $expected);

    }

    function testGetAllAssessments001_UT007() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->getAllAssessments('CIS4911');

        //expected output for unit test
        $expected = [
            ["ESSAYS",40,85],
            ["Total","",85]
        ];

        $this->assertEquals($return, $expected);

    }

    function testGetAllAssessments002_UT013() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->getAllAssessments('COP4610');

        //expected output for unit test
        $expected = [
            ["Homework",20,75.5],
            ["Quizes",30,78.5],
            ["Midterm",25,96],
            ["Final",25,95],
            ["Total","",86.4]
        ];

        $this->assertEquals($return, $expected);

    }

    function testPlotPoints001_UT008() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->plotPoints('CIS4911');

        //expected output for unit test
        $expected = [
            ["02-10"],
            [85]
        ];

        $this->assertEquals($return, $expected);

    }

    function testPlotPoints002_UT012() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->plotPoints('COP4610');

        //expected output for unit test
        $expected = [
            ["01-15","01-21","02-10","02-16","02-22","02-25","03-01","03-07"],
            [90,84.5,71.333333333333,81.333333333333
            ,86.222222222222,88.416666666667,89.25,85.45]
        ];

        $this->assertEquals($return, $expected);

    }

    function testGetGrades001_UT009() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->getGrades('ESSAYS','CIS4911');

        //expected output for unit test
        $expected = [
            ["Grade1",85]
        ];

        $this->assertEquals($return, $expected);

    }

    function testGetGrades002_UT011() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->getGrades('Homework','COP4610');

        //expected output for unit test
        $expected = [
            ["Grade1",90],
            ["Grade2",79],
            ["Grade3",45],
            ["Grade4",88]
        ];

        $this->assertEquals($return, $expected);

    }

}