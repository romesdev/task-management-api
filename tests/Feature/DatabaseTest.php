<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the user database.
     *
     * @return void
     */
    public function test_user_database()
    {
        User::factory()->count(3)->create();

        $this->assertDatabaseCount('users', 3);
    }

    /**
     * Test the user database.
     *
     * @return void
     */
    public function test_user_found_in_database()
    {
        User::factory()->create([
            'name' => 'Test Case',
            'email' => 'test@test.com',
        ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com',
        ]);
    }
}
