<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;

class FileControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @test
     * @dataProvider groupIdsProvider
     * @param int $groupId
     */
    public function test_example()
    {

        $response = $this->post('http://127.0.0.1:8000/api/check_in_m');

        $response->assertStatus(200);
    }

    /**
     * Data provider for group IDs.
     *
     * @return array
     */

    public function groupIdsProvider()
    {
        $faker = Faker::create();
        $groupIds = [];
        for ($i = 0; $i < 1000; $i++) {
            $groupIds[] = [$faker->numberBetween(1, 1000)];
        }

        return $groupIds;
    }
}
