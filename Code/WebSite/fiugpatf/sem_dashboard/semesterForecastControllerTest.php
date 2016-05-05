<?php
/**
 * Created by PhpStorm.
 * User: Lizette Mendoza
 * Date: 3/11/16
 * Time: 3:54 PM
 */

include_once '../common_files/dbconnector.php';
include_once 'semesterForecastController.php';

class SemesterForecastControllerTest extends PHPUnit_Framework_TestCase {

    function testGPAGoal001() {

        $sfc = new SemesterForecastController(27, 'lmend066');
        $return = $sfc->GPAGoal();

        //expected output for unit test
        $expected = [[3.2]];

        $this->assertEquals($return, $expected);
    }

    function testTakenAndRemaining001() {

        $sfc = new SemesterForecastController(12, 'newuser20');
        $return = $sfc->takenAndRemaining();

        //expected output for unit test
        $expected = [[24]];

        $this->assertEquals($return, $expected);
    }

    function testGradesAndCredits001() {

        $sfc = new SemesterForecastController(12, 'newuser20');
        $return = $sfc->gradesAndCredits();

        //expected output for unit test
        $expected = [
            ["A",1],
            ["B",3],
            ["B",3],
            ["A-",3],
            ["A-",3],
            ["C",3],
            ["B-",3],
            ["B",4],
            ["A",1],
            ["B+",3],
            ["A",1],
            ["B",3],
            ["A",1],
            ["B",3],
            ["D",3],
            ["B-",3],
            ["C",1],
            ["C",3],
            ["B",3],
            ["B+",3],
            ["A",3],
            ["B+",3],
            ["A",1],
            ["C+",4],
            ["B",4],
            ["A",1],
            ["C",4],
            ["C",4],
            ["B+",3],
            ["B+",3],
            ["A",3],
            ["P",1],
            ["B+",3],
            ["C",3],
            ["C",3],
            ["C",3],
            ["C",3],
            ["B-",3],
            ["C+",3],
            ["C",3],
            ["C+",3],
            ["C",3]
        ];

        $this->assertEquals($return, $expected);
    }

    function testCurrentCourses001() {

        $sfc = new SemesterForecastController(12, 'newuser20');
        $return = $sfc->currentCourses();

        //expected output for unit test
        $expected = [
            ["COP4555","Principles of Programming Languages",3,2,2],
            ["MAD3512","Introduction to Theory of Algorithm",3,2,2],
            ["CIS4911","Senior Project",3,2,2],
            ["COP4610","Operating Syst Princ",3,2,3]
        ];

        $this->assertEquals($return, $expected);
    }

}// end of SemesterForecastControllerTest()