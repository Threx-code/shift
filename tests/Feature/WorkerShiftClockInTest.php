<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Helpers\Helper;

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

    public function test_should_fail_if_the_user_already_clock_in_today()
    {
        $helper = new Helper();
        $response = $this->postJson(route("api.clock-in"), [
            'user_id' => 34
        ]);
        $response->assertStatus(422)
            ->assertJson([
                "error" => "Daily Work Limit Reached",
                "message" => "You have already clocked in {$helper->clockInTime()[0]}, your clock out is {$helper->clockInTime()[1]}"
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
        $response->assertStatus(422)
            ->assertJson([
                "error" => "Something went wrong",
                "message" => "This user hasn't clocked in today"
            ]);
    }

}
