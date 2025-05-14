<?php

namespace Tests\Feature;

use Database\Seeders\AdminSeeder;
use Database\Seeders\AttendancesAdminTestSeeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_all_users_attendance_can_be_viewed_correctly_for_a_specific_day()
    {   
        $this -> seed(AttendancesAdminTestSeeder::class);

        $user1 = User::where('name', '山田　太郎') -> first();

        $this -> assertDatabaseHas('users', [
            'name' => '山田　太郎',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user1 -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendanceUser1 = Attendance::where('user_id', $user1 -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendanceUser1 -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $user2 = User::where('name', '森　花子') -> first();

        $this -> assertDatabaseHas('users', [
            'name' => '森　花子',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user2 -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 10:00:00',
            'clock_out_at' => '2025-04-01 19:00:00',
        ]);

        $attendanceUser2 = Attendance::where('user_id', $user2 -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 10:00:00')
            -> where('clock_out_at', '2025-04-01 19:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendanceUser2 -> id,
            'interval_in_at' => '2025-04-01 13:00:00',
            'interval_out_at' => '2025-04-01 14:00:00',
        ]);

        $user3 = User::where('name', '伊藤　三郎') -> first();

        $this -> assertDatabaseHas('users', [
            'name' => '伊藤　三郎',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user3 -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 11:00:00',
            'clock_out_at' => '2025-04-01 20:00:00',
        ]);

        $attendanceUser3 = Attendance::where('user_id', $user3 -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 11:00:00')
            -> where('clock_out_at', '2025-04-01 20:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendanceUser3 -> id,
            'interval_in_at' => '2025-04-01 14:00:00',
            'interval_out_at' => '2025-04-01 15:00:00',
        ]);

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list?year=2025&month=4&day=1');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 2025年4月1日の勤務一覧');

        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('出勤時間');
        $response -> assertSee('09:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');
        $response -> assertSee('実働時間');
        $response -> assertSee('8:00');
        $response -> assertSee('詳細');
        $response -> assertSee('詳細ページへ');
        
        $response -> assertSee('名前');
        $response -> assertSee('森　花子');
        $response -> assertSee('出勤時間');
        $response -> assertSee('10:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('19:00');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');
        $response -> assertSee('実働時間');
        $response -> assertSee('8:00');
        $response -> assertSee('詳細');
        $response -> assertSee('詳細ページへ');

        $response -> assertSee('名前');
        $response -> assertSee('伊藤　三郎');
        $response -> assertSee('出勤時間');
        $response -> assertSee('11:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('20:00');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');
        $response -> assertSee('実働時間');
        $response -> assertSee('8:00');
        $response -> assertSee('詳細');
        $response -> assertSee('詳細ページへ');
    }

    /** @test */
    public function test_current_date_is_displayed_on_attendance_page()
    {
        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $nowDateTime = now() -> setTime(9, 0, 0);
        Carbon::setTestNow($nowDateTime);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list');

        $response -> assertStatus(200);

        $response -> assertSee($nowDateTime -> format('Y年n月j日の勤務一覧'));
    }

    /** @test */
    public function test_previous_day_attendance_is_shown_when_previous_button_clicked()
    {
        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $nowDate = now();
        Carbon::setTestNow($nowDate);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list');

        $response -> assertStatus(200);

        $response -> assertSee($nowDate -> format('Y年n月j日の勤務一覧'));

        $prevDate = $nowDate
                      -> copy()
                      -> subDay();

        $response -> assertSee('⬅前日');
        $response = $this -> get(route('admin.attendance.list', [
            'year' => $prevDate -> year,
            'month' => $prevDate-> month,
            'day' => $prevDate-> day,
        ]));

        $response -> assertStatus(200);

        $response -> assertSee($prevDate -> format('Y年n月j日の勤務一覧'));
    }

    /** @test */
    public function test_next_day_attendance_is_shown_when_next_button_clicked()
    {
        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $nowDate = now();
        Carbon::setTestNow($nowDate);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list');

        $response -> assertStatus(200);

        $response -> assertSee($nowDate -> format('Y年n月j日の勤務一覧'));

        $nextDate = $nowDate
                      -> copy()
                      -> addDay();

        $response -> assertSee('翌日➡');
        $response = $this -> get(route('admin.attendance.list', [
            'year' => $nextDate -> year,
            'month' => $nextDate-> month,
            'day' => $nextDate-> day,
        ]));

        $response -> assertStatus(200);

        $response -> assertSee($nextDate -> format('Y年n月j日の勤務一覧'));
    }
}
