<?php
class DbMock {

    public function __construct() {
        // Initialize the mock data

    }

    public function sql_query($sql, $unbuffered = false, $use_prepare = false): array
    {
        // Simulate executing a query
        return [
            ['title' => 'Mod 1', 'root' => 'mod1', 'version' => '1.0'],
            ['title' => 'Mod 2', 'root' => 'mod2', 'version' => '2.0']
        ];

    }

    public function sql_fetch_row($result) {
        // Simulate fetching a row from the result set
        return array_shift($result);
    }
}
