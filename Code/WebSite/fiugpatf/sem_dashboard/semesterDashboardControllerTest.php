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

    function testCurrentAssessments001() {

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

    function testGetGraphData002() {

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

    function testCourseLegend003() {

        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->courseLegend();

        //expected output for unit test
        $expected = ["COP4555","COP4610","CIS4911"];

        $this->assertEquals($return, $expected);
    }

    function testGetGradProgram004() {

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

    function testGetCurrentProgram005() {
        $sdc = new SemesterDashboardController(12, 'newuser20');
        $return = $sdc->getGradProgram();

        //expected output for unit test
        $expected = [
            ["Computer Science"]
        ];

        $this->assertEquals($return, $expected);
    }



}