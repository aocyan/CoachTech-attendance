<?php

namespace Tests\Feature;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceDateTimeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_correctly_retrieves_the_current_datetime()
    {
        $response = $this -> get('/register');
        $response -> assertStatus(200);

        $response = $this -> post('/register/store', [
            'name' => '山本　太郎',
            'email' => 'user@example.com',
            'password' => '1234abcd',
            'password_confirmation' => '1234abcd',
        ]);

        $user = Auth::user();

        $nowDateTime = now() -> setTime(9, 0, 0);
        Carbon::setTestNow($nowDateTime);
        
        $response -> assertRedirect('/attendance/default');

        $this -> get('/attendance/default');

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => null,
            'clock_in_at' => null,
            'clock_out_at' => null,
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> whereNull('date')
            -> whereNull('clock_in_at')
            -> whereNull('clock_out_at')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => null,
            'interval_out_at' => null,
        ]);

        $response = $this -> get('/attendance');
        $response -> assertStatus(200);

        $response -> assertSee($nowDateTime -> format('Y年n月j日'));
        $response -> assertSee($nowDateTime -> format('H:i'));
    }
}
