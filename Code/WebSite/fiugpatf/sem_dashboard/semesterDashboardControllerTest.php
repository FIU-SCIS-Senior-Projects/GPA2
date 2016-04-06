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

    function testCurrentAssessments001_UT001() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->currentAssessments();

        $expected = [
            ["COP4555","Principles of Programming Languages",3,82.72],
            ["MAD3512","Introduction to Theory of Algorithm",3,"No Grades"],
            ["CIS4911","Senior Project",3,85],
            ["COP4610","Operating Syst Princ",3,86.4]
        ];

        $this->assertEquals($return, $expected);
    }

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
            [[1452488400000,100],
                [1454302800000,93],
                [1456117200000,90.5],
                [1457928000000,82.723]],
            [[1452488400000,100],
                [1454302800000,84.5],
                [1456117200000,89.733333333333],
                [1457928000000,86.244444444444]],
            [[1452488400000,100],
                [1454302800000,100],
                [1456117200000,85],
                [1457928000000,85]]
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
            [1,85],
            [
                [1,"02-10"]
            ]
        ];

        $this->assertEquals($return, $expected);

    }

    function testPlotPoints002_UT012() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->plotPoints('COP4610');

        //expected output for unit test
        $expected = [
            [1,90],
            [2,84.5],
            [3,71.333333333333],
            [4,81.333333333333],
            [5,86.222222222222],
            [6,88.416666666667],
            [7,89.25],
            [8,85.45],
            [
                [1,"01-15"],
                [2,"01-21"],
                [3,"02-10"],
                [4,"02-16"],
                [5,"02-22"],
                [6,"02-25"],
                [7,"03-01"],
                [8,"03-07"]
            ]
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