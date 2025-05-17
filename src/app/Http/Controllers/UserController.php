<?php

namespace App\Http\Controllers;

use App\Http\Requests\DetailRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Leave;
use App\Models\Correction;
use App\Models\Interval;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(RegisterRequest $request)
	{
        $user = User::store();

		Auth::login($user);

		return redirect() -> route('user.default');
	}

    public function defaultAttend()
	{
		Attendance::defaultSettingAttend();
		Interval::defaultSettingInterval();

		return redirect() -> route('user.attend');
	}

	public function attend()
	{
		$dateTime = Attendance::nowDateTime();
		$attendanceData = Attendance::statusAttend();
		$intervalData = Interval::statusInterval();

		if ($attendanceData -> updated_at -> toDateString() !== now()
        								  -> toDateString()) {
			Attendance::dateChanges();
		}

		return view('user.attend', compact('dateTime', 'attendanceData','intervalData'));
	}

	public function clockIn()
	{
        Attendance::clockInTime();

		return redirect() -> route('user.attend');
	}

	public function clockOut()
	{
		Attendance::clockOutTime();
		
		return redirect() -> route('user.attend');
	}

	public function intervalIn()
	{
		Interval::intervalInTime();

		return redirect() -> route('user.attend');
	}

	public function intervalOut()
	{
		Interval::intervalOutTime();

		return redirect() -> route('user.attend');
	}

    public function index(Request $request)
	{
    	$year = $request -> query('year', now() -> year);
    	$month = $request -> query('month', now() -> month);
	
		$indexTime = Attendance::indexTime( Auth::user(), $year, $month );

		$MonthClockInTime = Attendance::getMonthClockTime( Auth::user(), $year, $month );
		$intervalTotalTime = Interval::getMonthIntervalTotalTime( Auth::user(), $year, $month );
		$workingTotalTime = Attendance::workingTotalTime( Auth::user(), $year, $month );

		return view('user.index', array_merge(
    		$indexTime,
    		$MonthClockInTime,
    		$intervalTotalTime,
    		$workingTotalTime,
    		['attendanceIds' => $MonthClockInTime['attendanceIds']]
		));
	}

    public function detail(Request $request, $id)
	{
		$user = User::findOrFail($id);
		$detailData = Attendance::detailData($user -> id,$request);

		$commentRequest = request() -> query('comment');		

    	if ($commentRequest !== null) {
        	$detailData['comment'] = urldecode( $commentRequest );
    	}

    	if ($detailData['correction'] === 'approved') {
        	$checkOtherCorrection = Correction::where( 'attendance_id', $detailData['attendance']->id)
    									-> latest('id')
    									-> first();
   	 	} else {
			$checkOtherCorrection = null;
		}

    	return view('user.detail', [
        	'user' => $user,
        	'attendance' => $detailData['attendance'],
			'intervals' => $detailData['intervals'],
			'correction' => $detailData['correction'],
			'comment' => $detailData['comment'],
			'id' => $id,
			'targetDate' => $detailData['targetDate'],
			'checkOtherCorrection' => $checkOtherCorrection,
    	]);
	}

	public function correction(DetailRequest $request,$id)
	{
		$date = $request -> input('date_data');

		$correction = Correction::store($request,$id,$date);
		Leave::store($request,$correction);

		return redirect() -> route('user.apply');
	}

    public function apply(Request $request)
	{
		if( Auth::guard('web')->check() ){
			$user = Auth::user();
			$corrections = Correction::userApply($user -> id);
			$searches = Correction::userSearch($request,$user -> id);
		} elseif( Auth::guard('admin')->check() ){
			$corrections = Correction::adminApply();
			$searches = Correction::adminSearch( $request );
		}

		return view('user.apply', compact('corrections','searches'));
	}
}
