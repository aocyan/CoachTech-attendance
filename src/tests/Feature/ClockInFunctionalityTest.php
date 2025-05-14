<?php

namespace Tests\Feature;

use Database\Seeders\AdminSeeder;
use Database\Seeders\ClockOutTestSeeder;
use Database\Seeders\ClockInTestSeeder;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClockInFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_clock_in_button_works_correctly()
    {
        $this -> seed(ClockInTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $nowDateTime = now() -> setTime(9, 0, 0);
        Carbon::setTestNow($nowDateTime);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('出勤する');

        $response = $this -> post('/attendance/clock/in');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => $nowDateTime -> toDateString(),
            'clock_in_at' => $nowDateTime,
            'clock_out_at' => null,
        ]);

        $response -> assertRedirect('/attendance');
        $response = $this -> get('/attendance');

        $response -> assertSee('勤務中');

        $response -> assertDontSee('勤務外');
        $response -> assertDontSee('休憩中');
        $response -> assertDontSee('退勤済');
    }

    /** @test */
    public function test_clock_in_can_only_be_performed_once_per_day()
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
        $response -> assertSee('一日お疲れさまでした');

        $response -> assertDontSee('出勤する');
        $response -> assertDontSee('休憩する');
    }

    /** @test */
    public function test_clock_in_time_is_visible_on_admin_dashboard()
    {
        $this -> seed(ClockInTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $nowDateTime = now() -> setTime(9, 0, 0);
        Carbon::setTestNow($nowDateTime);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('出勤する');

        $response = $this -> post('/attendance/clock/in');

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
            'status' => 'working',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => $nowDateTime -> toDateString(),
            'clock_in_at' => $nowDateTime,
            'clock_out_at' => null,
        ]);

        $response -> assertRedirect('/attendance');
        $response = $this -> get('/attendance');

        $response = $this -> post('/logout');

        $this -> seed(AdminSeeder::class);
        
        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response -> assertRedirect('/admin/attendance/list');
        $response = $this -> get('/admin/attendance/list');

        $response -> assertSee($nowDateTime -> format('Y年n月j日の勤務一覧'));
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('出勤時間');
        $response -> assertSee($nowDateTime -> format('H:i'));
    }
}
