<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TablesTest extends TestCase
{
    private $tableData = '{"Front":{"name":"Front","tables":{"75":{"name":"WallSide","visible":1},"108":{"name":"TablewithView","visible":0}}}}';
    /**
     * Test /tables API endpoint
     *
     * @return void
     */
    public function testTables()
    {
        $this->get('/tables')
            ->seeJson([
                "apikey" => ["The apikey field is required."]
            ]);

        $this->get('/tables?apikey=abcd')
            ->seeJson([
                "apikey" => ["The apikey must be a valid UUID.", "apikey is invalid."]
            ]);
        $this->get('/tables?apikey=3ee15a0d-9e2c-4a43-8492-eb51fe3c0ca0')
            ->seeJson([
                "apikey" => ["apikey is invalid."]
            ]);
    }
}
