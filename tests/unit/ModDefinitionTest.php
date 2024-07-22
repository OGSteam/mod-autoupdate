<?php


use PHPUnit\Framework\TestCase;

define("IN_SPYOGAME", true);

// Include the necessary dependencies
require_once 'tests/mocks/db_mock.php'; // Mock the database object
require_once 'core/functions.php'; // Include the functions file

// Set up the test fixtures
//$db = new DbMock(); // Create a mock database object

class ModDefinitionTest extends TestCase
{

    public function testGetInstalledModList()
    {
        define("TABLE_MOD", 'mod');
        global $db;

        // Set up the test fixtures
        $db = new DbMock(); // Create a mock database object

        // Set up the expected result
        $expectedResult = [
            ['name' => 'Mod 1', 'root' => 'mod1', 'version' => '1.0'],
            ['name' => 'Mod 2', 'root' => 'mod2', 'version' => '2.0'],
        ];

        // Call the function being tested
        $result = get_installed_mod_list();

        // Assert the expected result
        $this->assertEquals($expectedResult, $result);
    }
}
