<?php

namespace Tests\Feature;

use Database\Seeders\IntervalTestSeeder;
use Database\Seeders\WorkingTestSeeder;
use Database\Seeders\ClockOutTestSeeder;
use Database\Seeders\ClockInTestSeeder;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatusCheckFunctionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_attendance_status_displayed_correctly_when_off_duty()
    {
        $this -> seed(ClockInTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();
        
        $response -> assertRedirect('/attendance');

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');

        $response -> assertDontSee('勤務中');
        $response -> assertDontSee('休憩中');
        $response -> assertDontSee('退勤済');
    }

    /** @test */
    public function test_attendance_status_displayed_correctly_when_on_duty()
    {
        $this -> seed(WorkingTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();
        
        $response -> assertRedirect('/attendance');

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務中');

        $response -> assertDontSee('勤務外');
        $response -> assertDontSee('休憩中');
        $response -> assertDontSee('退勤済');
    }

    /** @test */
    public function test_attendance_status_displayed_correctly_when_on_interval()
    {
        $this -> seed(IntervalTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();
        
        $response -> assertRedirect('/attendance');

        $response = $this -> get('/attendance');
        $response -> assertSee('休憩中');

        $response -> assertDontSee('勤務外');
        $response -> assertDontSee('勤務中');
        $response -> assertDontSee('退勤済');
    }

    /** @test */
    public function test_attendance_status_displayed_correctly_when_off_work()
    {
        $this -> seed(ClockOutTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $response -> assertRedirect('/attendance');
        
        $response = $this -> get('/attendance');
        $response -> assertSee('退勤済');

        $response -> assertDontSee('勤務外');
        $response -> assertDontSee('勤務中');
        $response -> assertDontSee('休憩中');
    }
}
