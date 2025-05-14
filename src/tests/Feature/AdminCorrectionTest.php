<?php

namespace Tests\Feature;

use Database\Seeders\AdminSeeder;
use Database\Seeders\AdminCorrectionTestSeeder;
use App\MOdels\Admin;
use App\MOdels\Correction;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminCorrectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_all_pending_correction_requests_are_displayed()
    {
        $this -> seed(AdminCorrectionTestSeeder::class);

        $user1 = User::where('name', '山田　太郎') -> first();

        $firstAttendanceUser1 = Attendance::where('user_id', $user1 -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user1 -> id,
            'attendance_id' => $firstAttendanceUser1 -> id,
            'name' => '山田　太郎',
            'date' => '2025-04-01',
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        $secondAttendanceUser1 = Attendance::where('user_id', $user1 -> id)
            -> where('date', '2025-04-10')
            -> where('clock_in_at', '2025-04-10 09:00:00')
            -> where('clock_out_at', '2025-04-10 18:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user1 -> id,
            'attendance_id' => $secondAttendanceUser1 -> id,
            'name' => '山田　太郎',
            'date' => '2025-04-10',
            'comment' => '承認済みコメント',
            'status' => 'approved',
        ]);

        $thirdAttendanceUser1 = Attendance::where('user_id', $user1 -> id)
            -> where('date', '2025-04-20')
            -> where('clock_in_at', '2025-04-20 09:00:00')
            -> where('clock_out_at', '2025-04-20 18:00:00')
            -> first();

        $user2 = User::where('name', '森　花子') -> first();

        $firstAttendanceUser2 = Attendance::where('user_id', $user2 -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 10:00:00')
            -> where('clock_out_at', '2025-04-01 19:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user2 -> id,
            'attendance_id' => $firstAttendanceUser2 -> id,
            'name' => '森　花子',
            'date' => '2025-04-01',
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        $secondAttendanceUser2 = Attendance::where('user_id', $user2 -> id)
            -> where('date', '2025-04-10')
            -> where('clock_in_at', '2025-04-10 10:00:00')
            -> where('clock_out_at', '2025-04-10 19:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user2 -> id,
            'attendance_id' => $secondAttendanceUser2 -> id,
            'name' => '森　花子',
            'date' => '2025-04-10',
            'comment' => '承認済みコメント',
            'status' => 'approved',
        ]);

        $thirdAttendanceUser2 = Attendance::where('user_id', $user2 -> id)
            -> where('date', '2025-04-20')
            -> where('clock_in_at', '2025-04-20 10:00:00')
            -> where('clock_out_at', '2025-04-20 19:00:00')
            -> first();

        $user3 = User::where('name', '伊藤　三郎') -> first();

        $firstAttendanceUser3 = Attendance::where('user_id', $user3 -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 11:00:00')
            -> where('clock_out_at', '2025-04-01 20:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user3 -> id,
            'attendance_id' => $firstAttendanceUser3 -> id,
            'name' => '伊藤　三郎',
            'date' => '2025-04-01',
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        $secondAttendanceUser3 = Attendance::where('user_id', $user3 -> id)
            -> where('date', '2025-04-10')
            -> where('clock_in_at', '2025-04-10 11:00:00')
            -> where('clock_out_at', '2025-04-10 20:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user3 -> id,
            'attendance_id' => $secondAttendanceUser3 -> id,
            'name' => '伊藤　三郎',
            'date' => '2025-04-10',
            'comment' => '承認済みコメント',
            'status' => 'approved',
        ]);

        $thirdAttendanceUser3 = Attendance::where('user_id', $user3 -> id)
            -> where('date', '2025-04-20')
            -> where('clock_in_at', '2025-04-20 11:00:00')
            -> where('clock_out_at', '2025-04-20 20:00:00')
            -> first();

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list');

        $response -> assertStatus(200);

        $response = $this -> get('/stamp_correction_request/list');
        
        $response -> assertStatus(200);

        $response -> assertSee('┃ 申請一覧');

        $response -> assertSee('承認待ち');
        $response = $this -> post('/stamp_correction_request/list/search', [
            'status' => 'unapproved',
        ]);

        $response -> assertStatus(200);

        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('森　花子');
        $response -> assertSee('伊藤　三郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/01');
        $response -> assertSee('申請理由');
        $response -> assertSee('承認待ちコメント');

        $response -> assertDontSee('2025/04/10');
        $response -> assertDontSee('2025/04/20');
        $response -> assertDontSee('承認済みコメント');
    }

    /** @test */
    public function test_all_approved_corrections_are_displayed()
    {
        $this -> seed(AdminCorrectionTestSeeder::class);

        $user1 = User::where('name', '山田　太郎') -> first();

        $firstAttendanceUser1 = Attendance::where('user_id', $user1 -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user1 -> id,
            'attendance_id' => $firstAttendanceUser1 -> id,
            'name' => '山田　太郎',
            'date' => '2025-04-01',
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        $secondAttendanceUser1 = Attendance::where('user_id', $user1 -> id)
            -> where('date', '2025-04-10')
            -> where('clock_in_at', '2025-04-10 09:00:00')
            -> where('clock_out_at', '2025-04-10 18:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user1 -> id,
            'attendance_id' => $secondAttendanceUser1 -> id,
            'name' => '山田　太郎',
            'date' => '2025-04-10',
            'comment' => '承認済みコメント',
            'status' => 'approved',
        ]);

        $thirdAttendanceUser1 = Attendance::where('user_id', $user1 -> id)
            -> where('date', '2025-04-20')
            -> where('clock_in_at', '2025-04-20 09:00:00')
            -> where('clock_out_at', '2025-04-20 18:00:00')
            -> first();

        $user2 = User::where('name', '森　花子') -> first();

        $firstAttendanceUser2 = Attendance::where('user_id', $user2 -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 10:00:00')
            -> where('clock_out_at', '2025-04-01 19:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user2 -> id,
            'attendance_id' => $firstAttendanceUser2 -> id,
            'name' => '森　花子',
            'date' => '2025-04-01',
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        $secondAttendanceUser2 = Attendance::where('user_id', $user2 -> id)
            -> where('date', '2025-04-10')
            -> where('clock_in_at', '2025-04-10 10:00:00')
            -> where('clock_out_at', '2025-04-10 19:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user2 -> id,
            'attendance_id' => $secondAttendanceUser2 -> id,
            'name' => '森　花子',
            'date' => '2025-04-10',
            'comment' => '承認済みコメント',
            'status' => 'approved',
        ]);

        $thirdAttendanceUser2 = Attendance::where('user_id', $user2 -> id)
            -> where('date', '2025-04-20')
            -> where('clock_in_at', '2025-04-20 10:00:00')
            -> where('clock_out_at', '2025-04-20 19:00:00')
            -> first();

        $user3 = User::where('name', '伊藤　三郎') -> first();

        $firstAttendanceUser3 = Attendance::where('user_id', $user3 -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 11:00:00')
            -> where('clock_out_at', '2025-04-01 20:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user3 -> id,
            'attendance_id' => $firstAttendanceUser3 -> id,
            'name' => '伊藤　三郎',
            'date' => '2025-04-01',
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        $secondAttendanceUser3 = Attendance::where('user_id', $user3 -> id)
            -> where('date', '2025-04-10')
            -> where('clock_in_at', '2025-04-10 11:00:00')
            -> where('clock_out_at', '2025-04-10 20:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user3 -> id,
            'attendance_id' => $secondAttendanceUser3 -> id,
            'name' => '伊藤　三郎',
            'date' => '2025-04-10',
            'comment' => '承認済みコメント',
            'status' => 'approved',
        ]);

        $thirdAttendanceUser3 = Attendance::where('user_id', $user3 -> id)
            -> where('date', '2025-04-20')
            -> where('clock_in_at', '2025-04-20 11:00:00')
            -> where('clock_out_at', '2025-04-20 20:00:00')
            -> first();

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/admin/attendance/list');

        $response -> assertStatus(200);

        $response = $this -> get('/stamp_correction_request/list');
        
        $response -> assertStatus(200);

        $response -> assertSee('┃ 申請一覧');

        $response -> assertSee('承認済み');
        $response = $this -> post('/stamp_correction_request/list/search', [
            'status' => 'approved',
        ]);

        $response -> assertStatus(200);

        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('森　花子');
        $response -> assertSee('伊藤　三郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/10');
        $response -> assertSee('申請理由');
        $response -> assertSee('承認済みコメント');

        $response -> assertDontSee('2025/04/01');
        $response -> assertDontSee('2025/04/20');
        $response -> assertDontSee('承認待ちコメント');
    }

    /** @test */
    public function test_correction_request_details_are_displayed_correctly()
    {
        $this -> seed(AdminCorrectionTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user -> id,
            'attendance_id' => $attendance -> id,
            'name' => '山田　太郎',
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 10:00',
            'clock_out_at' => '2025-04-01 19:00',
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        $correction = Correction::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 10:00:00')
            -> where('clock_out_at', '2025-04-01 19:00:00')
            -> first();

        $this -> assertDatabaseHas('leaves', [
            'correction_id' => $correction -> id,
            'interval_in_at' => '2025-04-01 12:00',
            'interval_out_at' => '2025-04-01 13:00',
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

        $response = $this -> get('/admin/attendance/list');

        $response -> assertStatus(200);

        $response = $this -> get('/stamp_correction_request/list');
        
        $response -> assertStatus(200);

        $response -> assertSee('┃ 申請一覧');

        $response -> assertSee('承認待ち');
        $response = $this -> post('/stamp_correction_request/list/search', [
            'status' => 'unapproved',
        ]);

        $response -> assertStatus(200);

        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/01');
        $response -> assertSee('申請理由');
        $response -> assertSee('承認待ちコメント');

        $response -> assertSee('詳細ページへ');
        $response = $this -> get("/stamp_correction_request/approve/{$correction -> id}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('10:00');
        $response -> assertSee('19:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');
        $response -> assertSee('備考');
        $response -> assertSee('承認待ちコメント');
        $response -> assertSee('承認する');
    }

    /** @test */
    public function test_correction_request_approval_process_works_correctly()
    {
        $this -> seed(AdminCorrectionTestSeeder::class);

        $user = User::where('name', '山田　太郎') -> first();

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('corrections', [
            'user_id' => $user -> id,
            'attendance_id' => $attendance -> id,
            'name' => '山田　太郎',
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 10:00',
            'clock_out_at' => '2025-04-01 19:00',
            'comment' => '承認待ちコメント',
            'status' => 'unapproved',
        ]);

        $correction = Correction::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 10:00:00')
            -> where('clock_out_at', '2025-04-01 19:00:00')
            -> first();

        $this -> assertDatabaseHas('leaves', [
            'correction_id' => $correction -> id,
            'interval_in_at' => '2025-04-01 12:00',
            'interval_out_at' => '2025-04-01 13:00',
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
        $response -> assertSee('9:00');

        $response = $this -> get('/stamp_correction_request/list');
        
        $response -> assertStatus(200);

        $response -> assertSee('┃ 申請一覧');

        $response -> assertSee('承認待ち');
        $response = $this -> post('/stamp_correction_request/list/search', [
            'status' => 'unapproved',
        ]);

        $response -> assertStatus(200);

        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/01');
        $response -> assertSee('申請理由');
        $response -> assertSee('承認待ちコメント');

        $response -> assertSee('詳細ページへ');
        $response = $this -> get("/stamp_correction_request/approve/{$correction -> id}");

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('10:00');
        $response -> assertSee('19:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');
        $response -> assertSee('備考');
        $response -> assertSee('承認待ちコメント');

        $response -> assertSee('承認する');
        $response = $this -> post("/stamp_correction_request/approve/correction/{$correction -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in' => '10:00',
            'clock_out' => '19:00',
            'interval_in' => ['12:00'],
            'interval_out' => ['13:00'],
            'comment' => '承認待ちコメント',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 10:00',
            'clock_out_at' => '2025-04-01 19:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 10:00:00')
            -> where('clock_out_at', '2025-04-01 19:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00',
            'interval_out_at' => '2025-04-01 13:00',
        ]);
        
        $response = $this -> get("stamp_correction_request/approve/{$correction -> id}");

        $response -> assertStatus(200);

        $response = $this -> get('/admin/attendance/list?year=2025&month=4&day=1');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 2025年4月1日の勤務一覧');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('出勤時間');
        $response -> assertSee('10:00');
        $response -> assertSee('退勤時間');
        $response -> assertSee('19:00');
        $response -> assertSee('休憩時間');
        $response -> assertSee('1:00');
        $response -> assertSee('実働時間');
        $response -> assertSee('9:00');
    }
}
