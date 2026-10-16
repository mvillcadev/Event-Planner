<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_employee_can_access_dashboard(): void
    {
        $employee = \App\Models\User::where('email', 'empleado@vanessa.com')->first();
        if ($employee) {
            $response = $this->actingAs($employee)->get('/empleado/dashboard');
            $response->assertStatus(200);
        }
    }
}
