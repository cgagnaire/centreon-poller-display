<?php
/**
 * Copyright 2016 Centreon
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use \Centreon\Test\Mock\CentreonDB;
use \CentreonPollerDisplayCentral\ConfigGenerate\Centreon\ContactgroupServicegroupRelation;


/**
 * @package centreon-poller-display-central
 * @version 1.0.0
 * @author Centreon
 */
class CentreonPollerDisplayCentral_ContactgroupServicegroupRelation extends PHPUnit_Framework_TestCase
{
    protected static $db;
    protected static $pollerDisplay;
    protected static $contact;
    protected static $objectListIn;
    protected static $objectListOut;

    public function setUp()
    {
        self::$db = new CentreonDB();
        self::$pollerDisplay = 1;
        self::$contact = new ContactgroupServicegroupRelation(self::$db, self::$pollerDisplay);
        self::$objectListIn = array(
            array(
                'sgr_id' => '1',
                'host_host_id' => '1',
                'service_service_id' => '1',
                'servicegroup_sg_id' => '1'

            )
        );
        self::$objectListOut = array(
            array(
                'servicegroup_sg_id' => '1',
                'contactgroup_cg_id' => '42'
            )
        );
    }

    public function tearDown()
    {
        self::$db = null;
    }

    public function testGetList()
    {
        self::$db->addResultSet(
            'SELECT * FROM contactgroup_servicegroup_relation WHERE servicegroup_sg_id IN (1)',
            array(
                array(
                    'servicegroup_sg_id' => '1',
                    'contactgroup_cg_id' => '42'
                )
            )
        );

        $sql = self::$contact->getList(self::$objectListIn);
        $this->assertEquals($sql, self::$objectListOut);
    }

    public function testGenerateSql()
    {

        $expectedResult = 'DELETE FROM contactgroup_servicegroup_relation;
TRUNCATE contactgroup_servicegroup_relation;
INSERT INTO `contactgroup_servicegroup_relation` (`servicegroup_sg_id`,`contactgroup_cg_id`) VALUES (\'1\',\'42\');';

        $sql = self::$contact->generateSql(self::$objectListOut);
        $this->assertEquals($sql, $expectedResult);
    }
}
