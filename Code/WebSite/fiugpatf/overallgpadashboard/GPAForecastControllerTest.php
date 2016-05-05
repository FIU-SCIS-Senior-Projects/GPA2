<?php
/**
 * Created by PhpStorm.
 * User: Lizette Mendoza
 * Date: 3/23/16
 * Time: 2:04 PM
 */

include_once '../common_files/dbconnector.php';
include_once 'GPAForecastController.php';

class GPAForecastControllerTest extends PHPUnit_Framework_TestCase {

    function testGPAGoal001()
    {
        $gft = new GPAForecastController(12, 'newuser20');
        $return = $gft->GPAGoal();

        //expected output for unit test
        $expected = [[2.78]];

        $this->assertEquals($return, $expected);
    }

    function testTakenAndRemaining002()
    {
        $gft = new GPAForecastController(12, 'newuser20');
        $return = $gft->takenAndRemaining();

        //expected output for unit test
        $expected = [[24]];

        $this->assertEquals($return, $expected);

    }

    function testGradesAndCredits003()
    {
        $gft = new GPAForecastController(12, 'newuser20');
        $return = $gft->gradesAndCredits();

        //expected output for unit test
        $expected = [
            ["A",1],
            ["A",1],
            ["B",3],
            ["B-",3],
            ["B",3],
            ["B",3],
            ["A-",3],
            ["A-",3],
            ["B",3],
            ["C",3],
            ["D",3],
            ["B-",3],
            ["C",1],
            ["C",3],
            ["B",3],
            ["B+",3],
            ["A",3],
            ["A",1],
            ["B+",3],
            ["C+",4],
            ["B+",3],
            ["B+",3],
            ["A",1],
            ["A",1],
            ["B",4],
            ["B",4],
            ["A",1],
            ["B+",3],
            ["C",4],
            ["P",1],
            ["A",3],
            ["B+",3],
            ["C",4],
            ["B-",3],
            ["C",3],
            ["C",3],
            ["C",3],
            ["C",3],
            ["C",3],
            ["C+",3],
            ["C",3],
            ["C+",3]
        ];

        $this->assertEquals($return, $expected);

    }

    function testRemainingCourses004()
    {
        $gft = new GPAForecastController(12, 'newuser20');
        $return = $gft->remainingCourses();

        //expected output for unit test
        $expected = [
            ["CIS4911","Senior Project",3,2,2],
            ["COP4555","Principles of Programming Languages",3,2,2],
            ["COP4610","Operating Syst Princ",3,2,3],
            ["MAD3512","Introduction to Theory of Algorithm",3,2,2],
            ["COM3417","Communication in Film",3,3,1],
            ["CRW2001","ntroduction to Creative Writing",3,1,2],
            ["SPC2608","Public Speaking",3,1,3],
            ["CNT4713","Net-centric Computing",3,3,1]
        ];

        $this->assertEquals($return, $expected);
    }
}