<?php

namespace Tests\Feature;

use Database\Seeders\AdminSeeder;
use Database\Seeders\AttendanceIndexTestSeeder;
use App\Models\Correction;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DetailCorrectionUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_error_message_is_shown_when_clock_in_is_after_clock_out()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in' => '18:00',
            'clock_out' => '09:00',
            'interval_in' => ['12:00'],
            'interval_out' => ['13:00'],
            'comment' => 'テスト'
        ]);

        $response -> assertRedirect("/attendance/{$user -> id}?date={$detailDate}");
        $response -> assertStatus(302);

        $response -> assertSessionHasErrors(['clock_in']);

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertSeeText('出勤時間もしくは退勤時間が不適切な値です');
    }

    /** @test */
    public function test_user_error_displayed_when_break_start_after_clock_out()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in'     => '09:00',
            'clock_out'    => '18:00',
            'interval_in' => ['19:00'],
            'interval_out' => ['20:00'],
            'comment' => 'テスト',
        ]);

        $response -> assertRedirect("/attendance/{$user -> id}?date={$detailDate}");
        
        $response -> assertStatus(302);

        $response -> assertSessionHasErrors(['interval_out.0']);

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertSeeText('休憩時間が勤務時間外です');
    }

    /** @test */
    public function test_user_interval_out_is_after_clock_out()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in'     => '09:00',
            'clock_out'    => '18:00',
            'interval_in' => ['17:30'],
            'interval_out' => ['18:30'],
            'comment' => 'テスト',
        ]);

        $response -> assertRedirect("/attendance/{$user -> id}?date={$detailDate}");
        
        $response -> assertStatus(302);

        $response -> assertSessionHasErrors(['interval_out.0']);

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertSeeText('休憩時間が勤務時間外です');
    }

    /** @test */
    public function test_user_error_message_is_displayed_when_remark_field_is_empty()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertStatus(200);

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in'     => '09:00',
            'clock_out'    => '18:00',
            'interval_in' => ['12:00'],
            'interval_out' => ['13:00'],
            'comment' => '',
        ]);

        $response -> assertStatus(302);
        
        $response -> assertSessionHasErrors('comment');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertSeeText('備考を記入してください');
    }

    /** @test */
    public function test_correction_request_is_processed()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in' => '07:00',
            'clock_out' => '19:00',
            'interval_in' => ['12:00'],
            'interval_out' => ['13:00'],
            'comment' => 'テスト',
        ]);

        $response -> assertRedirect('/stamp_correction_request/list');
        $response = $this -> get('/stamp_correction_request/list');

        $response = $this -> post('/logout');

        $this -> seed(AdminSeeder::class);

        $adminUser = Admin::where('email', 'admin@example.com') -> first();

        $response = $this -> get('/admin/login');

        $response -> assertStatus(200);

        $response = $this -> post('/admin/login/certification', [
            'email' => 'admin@example.com',
            'password' => '1234abcd',
        ]);

        $correction = Correction::where('user_id', $user->id)
            -> where('date', $detailDate)
            -> latest()
            -> first();

        $this -> actingAs($adminUser, 'admin');

        $response = $this -> get('/stamp_correction_request/list');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 申請一覧');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/01');
        $response -> assertSee('申請理由');
        $response -> assertSee('テスト');

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
        $response -> assertSee('07:00');
        $response -> assertSee('19:00');
        $response -> assertSee('備考');
        $response -> assertSee('テスト');
        $response -> assertSee('承認する');
    }

    /** @test */
    public function test_all_pending_corrections_are_shown_for_authenticated_user()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $firstAttendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $firstAttendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-10',
            'clock_in_at' => '2025-04-10 09:00:00',
            'clock_out_at' => '2025-04-10 18:00:00',
        ]);

        $secondAttendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-10')
            -> where('clock_in_at', '2025-04-10 09:00:00')
            -> where('clock_out_at', '2025-04-10 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $secondAttendance -> id,
            'interval_in_at' => '2025-04-10 12:00:00',
            'interval_out_at' => '2025-04-10 13:00:00',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-20',
            'clock_in_at' => '2025-04-20 09:00:00',
            'clock_out_at' => '2025-04-20 18:00:00',
        ]);

        $thirdAttendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-20')
            -> where('clock_in_at', '2025-04-20 09:00:00')
            -> where('clock_out_at', '2025-04-20 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $thirdAttendance -> id,
            'interval_in_at' => '2025-04-20 12:00:00',
            'interval_out_at' => '2025-04-20 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $firstDetailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$firstDetailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $firstDetailDate,
            'clock_in' => '07:00',
            'clock_out' => '19:00',
            'interval_in' => ['12:00'],
            'interval_out' => ['13:00'],
            'comment' => '修正1',
        ]);

        $response -> assertRedirect('/stamp_correction_request/list');
        $response = $this -> get('/stamp_correction_request/list');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $secondDetailDate = Carbon::create(2025, 4, 10) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/10');
        $response -> assertSee('詳細ページへ');


        $response = $this -> get("/attendance/{$user -> id}?date={$secondDetailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 10日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $secondDetailDate,
            'clock_in' => '10:00',
            'clock_out' => '17:00',
            'interval_in' => ['12:00'],
            'interval_out' => ['13:00'],
            'comment' => '修正2',
        ]);

        $response -> assertRedirect('/stamp_correction_request/list');
        $response = $this -> get('/stamp_correction_request/list');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $thirdDetailDate = Carbon::create(2025, 4, 20) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/20');
        $response -> assertSee('詳細ページへ');


        $response = $this -> get("/attendance/{$user -> id}?date={$thirdDetailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 20日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $thirdDetailDate,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'interval_in' => ['11:30'],
            'interval_out' => ['12:30'],
            'comment' => '修正3',
        ]);

        $response -> assertRedirect('/stamp_correction_request/list');
        $response = $this -> get('/stamp_correction_request/list');

        $response -> assertSee('┃ 申請一覧');

        $response -> assertSee('状態');
        $response -> assertSee('承認待ち');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/01');
        $response -> assertSee('申請理由');
        $response -> assertSee('修正1');

        $response -> assertSee('状態');
        $response -> assertSee('承認待ち');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/10');
        $response -> assertSee('申請理由');
        $response -> assertSee('修正2');

        $response -> assertSee('状態');
        $response -> assertSee('承認待ち');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/20');
        $response -> assertSee('申請理由');
        $response -> assertSee('修正3');
    }

    /** @test */
    public function test_admin_approved_corrections_are_shown_in_approved_list()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $firstAttendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $firstAttendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-10',
            'clock_in_at' => '2025-04-10 09:00:00',
            'clock_out_at' => '2025-04-10 18:00:00',
        ]);

        $secondAttendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-10')
            -> where('clock_in_at', '2025-04-10 09:00:00')
            -> where('clock_out_at', '2025-04-10 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $secondAttendance -> id,
            'interval_in_at' => '2025-04-10 12:00:00',
            'interval_out_at' => '2025-04-10 13:00:00',
        ]);

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-20',
            'clock_in_at' => '2025-04-20 09:00:00',
            'clock_out_at' => '2025-04-20 18:00:00',
        ]);

        $thirdAttendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-20')
            -> where('clock_in_at', '2025-04-20 09:00:00')
            -> where('clock_out_at', '2025-04-20 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $thirdAttendance -> id,
            'interval_in_at' => '2025-04-20 12:00:00',
            'interval_out_at' => '2025-04-20 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $firstDetailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$firstDetailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $firstDetailDate,
            'clock_in' => '07:00',
            'clock_out' => '19:00',
            'interval_in' => ['12:00'],
            'interval_out' => ['13:00'],
            'comment' => '修正1',
        ]);

        $response -> assertRedirect('/stamp_correction_request/list');
        $response = $this -> get('/stamp_correction_request/list');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $secondDetailDate = Carbon::create(2025, 4, 10) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/10');
        $response -> assertSee('詳細ページへ');


        $response = $this -> get("/attendance/{$user -> id}?date={$secondDetailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 10日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $secondDetailDate,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'interval_in' => ['12:30'],
            'interval_out' => ['13:30'],
            'comment' => '修正2',
        ]);

        $response -> assertRedirect('/stamp_correction_request/list');
        $response = $this -> get('/stamp_correction_request/list');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $thirdDetailDate = Carbon::create(2025, 4, 20) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/20');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$thirdDetailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 20日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $thirdDetailDate,
            'clock_in' => '10:00',
            'clock_out' => '20:00',
            'interval_in' => ['13:00'],
            'interval_out' => ['14:00'],
            'comment' => '修正3',
        ]);

        $response -> assertRedirect('/stamp_correction_request/list');
        $response = $this -> get('/stamp_correction_request/list');

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

        $firstCorrection = Correction::where('user_id', $user->id)
            -> where('date', $firstDetailDate)
            -> latest()
            -> first();

        $response = $this -> get('/stamp_correction_request/list');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 申請一覧');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/01');
        $response -> assertSee('申請理由');
        $response -> assertSee('修正1');

        $response -> assertSee('詳細ページへ');
        $response = $this -> get("/stamp_correction_request/approve/{$firstCorrection -> id}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('07:00');
        $response -> assertSee('19:00');
        $response -> assertSee('備考');
        $response -> assertSee('修正1');

        $response -> assertSee('承認する');

        $response = $this -> post("/stamp_correction_request/approve/correction/{$firstCorrection -> id}", [
            'name' => $user -> name,
            'date_data' => $firstDetailDate,
            'clock_in' => '07:00',
            'clock_out' => '19:00',
            'interval_in' => ['12:00'],
            'interval_out' => ['13:00'],
            'comment' => '修正1',
        ]);

        $response -> assertRedirect("/stamp_correction_request/approve/{$firstCorrection -> id}");
        $response = $this -> get("/stamp_correction_request/approve/{$firstCorrection -> id}");

        $secondCorrection = Correction::where('user_id', $user->id)
            -> where('date', $secondDetailDate)
            -> latest()
            -> first();

        $response = $this -> get('/stamp_correction_request/list');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 申請一覧');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/10');
        $response -> assertSee('申請理由');
        $response -> assertSee('修正2');

        $response -> assertSee('詳細ページへ');
        $response = $this -> get("/stamp_correction_request/approve/{$secondCorrection -> id}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月10日');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:30');
        $response -> assertSee('13:30');
        $response -> assertSee('備考');
        $response -> assertSee('修正2');

        $response -> assertSee('承認する');

        $response = $this -> post("/stamp_correction_request/approve/correction/{$secondCorrection -> id}", [
            'name' => $user -> name,
            'date_data' => $secondDetailDate,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'interval_in' => ['12:30'],
            'interval_out' => ['13:30'],
            'comment' => '修正2',
        ]);

        $response -> assertRedirect("/stamp_correction_request/approve/{$secondCorrection -> id}");
        $response = $this -> get("/stamp_correction_request/approve/{$secondCorrection -> id}");

        $response = $this -> get('/stamp_correction_request/list');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 申請一覧');

        $response -> assertSee('状態');
        $response -> assertSee('承認済み');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/01');
        $response -> assertSee('申請理由');
        $response -> assertSee('修正1');

        $response -> assertSee('状態');
        $response -> assertSee('承認済み');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/10');
        $response -> assertSee('申請理由');
        $response -> assertSee('修正2');

        $response -> assertSee('状態');
        $response -> assertSee('承認待ち');
        $response -> assertSee('名前');
        $response -> assertSee('山田　太郎');
        $response -> assertSee('対象日時');
        $response -> assertSee('2025/04/20');
        $response -> assertSee('申請理由');
        $response -> assertSee('修正3');
    }

    /** @test */
    public function test_clicking_detail_button_navigates_to_correction_detail_page()
    {
        $this -> seed(AttendanceIndexTestSeeder::class);

        $response = $this -> get('/login');

        $response -> assertStatus(200);

        $response = $this -> post('/login/certification', [
            'email' => 'user@example.com',
            'password' => '1234abcd',
        ]);

        $user = Auth::user();

        $this -> assertDatabaseHas('attendances', [
            'user_id' => $user -> id,
            'date' => '2025-04-01',
            'clock_in_at' => '2025-04-01 09:00:00',
            'clock_out_at' => '2025-04-01 18:00:00',
        ]);

        $attendance = Attendance::where('user_id', $user -> id)
            -> where('date', '2025-04-01')
            -> where('clock_in_at', '2025-04-01 09:00:00')
            -> where('clock_out_at', '2025-04-01 18:00:00')
            -> first();

        $this -> assertDatabaseHas('intervals', [
            'attendance_id' => $attendance -> id,
            'interval_in_at' => '2025-04-01 12:00:00',
            'interval_out_at' => '2025-04-01 13:00:00',
        ]);

        $response = $this -> get('/attendance');
        $response -> assertSee('勤務外');
        $response -> assertSee('勤務一覧');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $detailDate = Carbon::create(2025, 4, 1) -> toDateString();

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('09:00');
        $response -> assertSee('18:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');
        $response -> assertSee('備考');
        $response -> assertSee('');

        $response = $this -> post("/attendance/correction/{$user -> id}", [
            'name' => $user -> name,
            'date_data' => $detailDate,
            'clock_in' => '07:00',
            'clock_out' => '19:00',
            'interval_in' => ['12:00'],
            'interval_out' => ['13:00'],
            'comment' => 'テスト',
        ]);

        $response -> assertRedirect('/stamp_correction_request/list');
        $response = $this -> get('/stamp_correction_request/list');

        $response = $this -> get('/attendance/list?year=2025&month=4');

        $response -> assertStatus(200);

        $response -> assertSee('┃ 勤務一覧');
        $response -> assertSee('04/01');
        $response -> assertSee('詳細ページへ');

        $response = $this -> get("/attendance/{$user -> id}?date={$detailDate}");

        $response -> assertSee('┃ 勤務詳細');
        $response -> assertSee('日付');
        $response -> assertSee('2025年');
        $response -> assertSee('4月 1日');
        $response -> assertSee('出勤・退勤');
        $response -> assertSee('07:00');
        $response -> assertSee('19:00');
        $response -> assertSee('休憩1');
        $response -> assertSee('12:00');
        $response -> assertSee('13:00');
        $response -> assertSee('備考');
        $response -> assertSee('テスト');
        $response -> assertSee('※　承認待ちのため修正はできません');
    }
}

