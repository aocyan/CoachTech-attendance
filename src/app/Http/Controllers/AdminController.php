<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminDetailRequest;
use App\Models\Comment;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
	{
		$date = Admin::indexToRedirectCorrection(
        	$request -> query('year'),
        	$request -> query('month'),
        	$request -> query('day'),
        	$request -> query('date') ,
    	);

		$targetDate = $date['year'] . '-' . $date['month'] . '-' . $date['day'];

		$attendances = Attendance::whereDate('clock_in_at', $targetDate) -> get();

		$attendanceIds = $attendances -> mapWithKeys(function ($attendance) {
    		return [$attendance->user_id => $attendance->id];
		});

		$users = User::all();

		foreach ($users as $user) {
    		$user->attendance_id = (int)$attendanceIds->get($user->id);
		}

    	$indexTime = Admin::indexTime($date['year'], $date['month'], $date['day']);
    	$clockTimes = Admin::getMonthClockTime($date['year'], $date['month'], $date['day']);
    	$intervalTimes = Admin::getMonthIntervalTotalTime($date['year'], $date['month'], $date['day']);
    	$workingTimes = Admin::workingTotalTime($date['year'], $date['month'], $date['day']);

		return view('admin.index', [
			'users' => $users,
			'userDataDate' => $indexTime['userDataDate'],
        	'formatDate' => $indexTime['formatDate'],
        	'prevDate' => $indexTime['prevDate'],
        	'nextDate' => $indexTime['nextDate'],
        	'clockInTimes' => $clockTimes['clockInTimes'],
         	'clockOutTimes' => $clockTimes['clockOutTimes'],
        	'intervalTotalTimes' => $intervalTimes['intervalTotalTimes'],
        	'workingTotalTimes' => $workingTimes['workingTotalTimes'],
			'attendanceId' => $attendanceIds,
    	]);
	}

	public function indexSearch(Request $request)
	{
		$searchDate = $request -> input('search_name');

		$date = \Carbon\Carbon::createFromFormat('Y-m-d', $searchDate);
    	$year = $date->year;
    	$month = $date->month;
    	$day = $date->day;

		$users = User::all();
		$indexTime = Admin::indexTime($year, $month, $day);
		$clockTimes = Admin::getMonthClockTime($year, $month, $day);
    	$intervalTimes = Admin::getMonthIntervalTotalTime($year, $month, $day);
    	$workingTimes = Admin::workingTotalTime($year, $month, $day);

		return view('admin.index', [
			'users' => $users,
			'userDataDate' => $indexTime['userDataDate'],
        	'formatDate' => $indexTime['formatDate'],
        	'prevDate' => $indexTime['prevDate'],
        	'nextDate' => $indexTime['nextDate'],
        	'clockInTimes' => $clockTimes['clockInTimes'],
        	'clockOutTimes' => $clockTimes['clockOutTimes'],
        	'intervalTotalTimes' => $intervalTimes['intervalTotalTimes'],
        	'workingTotalTimes' => $workingTimes['workingTotalTimes'],
    	]);
	}

	public function correction(AdminDetailRequest $request, $id)
	{
		$date = $request -> input('date_data');

		$userId = $id;
 
		$attendance = Admin::newData($userId, $date,$request);
		$attendance = Admin::updateData($userId, $date,$request);

		$comment = Comment::where('attendance_id', $attendance->id) -> first();

		session([ 'adminComment' => $comment ]);

		return redirect() -> route('admin.attendance.list', [
        	'date' => $date,
    	]);
	}
}