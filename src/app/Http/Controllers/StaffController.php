<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
	{
		$staffs = Staff::staffIndex();

		return view('staff.index',compact('staffs'));
	}

    public function attendList()
	{
		return view('staff.attend_index');
	}
}
