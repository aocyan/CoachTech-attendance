<?php

namespace App\Http\Controllers;

use App\Models\Correction;
use App\Models\Interval;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
	{
		$staffs = Staff::staffIndex();

		return view('staff.index',compact('staffs'));
	}

    public function attendList(Request $request,$id)
	{
		$user = User::findOrFail($id);

		$year = $request -> query('year', now() -> year);
    	$month = $request -> query('month', now() -> month);
		
		$indexTime = Attendance::indexTime($year, $month);

		$MonthClockInTime = Attendance::getMonthClockTime($user, $year, $month);
		$intervalTotalTime = Interval::getMonthIntervalTotalTime($user, $year, $month);
		$workingTotalTime = Attendance::workingTotalTime($user, $year, $month);

		return view('staff.attend_index', array_merge(
        	$indexTime,
        	$MonthClockInTime,
        	$intervalTotalTime,
        	$workingTotalTime,
        	[
            	'attendanceIds' => $MonthClockInTime['attendanceIds'],
            	'user' => $user,
				'id' => $id,
        	]
    	));
	}

	public function csv(Request $request, $id)
	{
		$user = User::findOrFail($id);

		$year = $request -> query('year', now()->year);
    	$month = $request -> query('month', now()->month);

    	return Staff::exportMonthCsv($user, $year, $month);
	}

	public function detail($attendance_correct_request)
	{
		$correction = Correction::findOrFail($attendance_correct_request);

		$detailData = Staff::detailData($correction-> id);

		return view('staff.correction', [
        	'correction' => $correction,
        	'intervals' => $detailData['intervals'],
    	]);
	}

	public function correction($attendance_correct_request)
	{

		$correction = Correction::findOrFail($attendance_correct_request);

		Staff::attendanceDataUpdate($correction -> id);

		return redirect()->route('staff.detail', [ 'attendance_correct_request' => $correction->id ]);
	}
}
