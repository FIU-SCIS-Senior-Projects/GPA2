<?php
/**
 * Created by PhpStorm.
 * User: Lizette Mendoza
 * Date: 3/23/16
 * Time: 6:32 PM
 */

include_once'../common_files/dbconnector.php';
include_once 'OvrlDashController.php';

class OvrlDashControllerTest extends PHPUnit_Framework_TestCase {

    //Purpose: Ensure that all the in-progress and incomplete classes along with their weight and relevance values are returned for a user with imported Panther Audit
    function testCheckWeightAndRelevance001_UT001() {
        $odc = new OverallDashboardController(12, 'newuser20');
        $return = $odc->checkWeightAndRelevance();

        $expected = [
            ["CIS4911",2,2],
            ["COP4555",2,2],
            ["COP4610",2,3],
            ["MAD3512",2,2],
            ["COM3417",3,1],
            ["CRW2001",1,2],
            ["SPC2608",1,3],
            ["CNT4713",3,1]
        ];

        $this->assertEquals($return, $expected);
    }

    //Purpose: Ensure that a GPA is generated for each semester completed at FIU for a user with imported Panther Audit
    function testGetGraphData001_UT002() {
        $odc = new OverallDashboardController(12, 'newuser20');
        $return = $odc->getGraphData();

        $expected = [
            ["Fall'10",3.0721428571429],
            ["Fall'11",3.0780769230769],
            ["Spr'11",2.7702564102564],
            ["Fall'12",2.8895555555556],
            ["Spr'12",2.9376271186441],
            ["Sum'12",2.975652173913],
            ["Fall'13",2.9526923076923],
            ["Spr'13",2.9579545454545],
            ["Fall'14",2.9181914893617],
            ["Spr'14",2.8379611650485],
            ["Sum'14",2.8142452830189],
            ["Spr'15",2.7677391304348]
        ];

        $this->assertEquals($return, $expected);
    }

    //Purpose: Ensure that no GPA data is returned for a new user who has not imported a Panther audit
    function testGetGraphData002_UT003() {
        $odc = new OverallDashboardController(27, 'lmend066');
        $return = $odc->getGraphData();

        $expected = "No data for graph";

        $this->assertEquals($return, $expected);
    }




}