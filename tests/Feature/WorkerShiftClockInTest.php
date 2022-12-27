<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkerShiftClockInTest extends TestCase
{
    public function test_should_fail_when_no_parameter_is_provided()
    {
        $response = $this->postJson(route("api.clock-in"), []);
        $response->assertStatus(422)
            ->assertJson([
                'error' => 'The given data was invalid',
                'message' => "The user id field is required.",
            ]);
    }

    public function test_should_fail_when_wrong_data_type_is_passed()
    {
        $response = $this->postJson(route("api.clock-in"), [
            'user_id' => 'string'
        ]);
        $response->assertStatus(422)
        ->assertJson([
            'error' => 'The given data was invalid',
            'message' => 'The user id must be an integer.'
            ]);
    }

    public function test_should_return_if_the_user_already_clock_in_today()
    {
        $response = $this->postJson(route("api.clock-in"), [
            'user_id' => 34
        ]);
        $response->assertStatus(422)
            ->assertJson([
                "error" => "Daily Work Limit Reached",
                "message" => "You have already clocked in 16:01, your clock out is 00:01"
            ]);
    }

    /**
     * @throws Exception
     */
    public function test_should_pass_when_a_user_has_not_clock_in_today()
    {
        $response = $this->postJson(route("api.clock-in"), [
            'user_id' => random_int(2, 1000)
        ]);
        $response->assertOk()->assertJson([
                "clock_in" => true
        ]);
    }

}
