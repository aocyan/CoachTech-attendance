<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('admin.auth.login');
    }

    public function store(AdminLoginRequest $request)
    {
        $credentials = $request -> only(
                            'email', 
                            'password'
                        );

        if (Auth::guard('admin') -> attempt($credentials, $request -> boolean('remember'))) {
            $request 
                -> session() 
                -> regenerate();

            return redirect() -> route('admin.attendance.list');
        }else {
            return back() -> withErrors([
                'email' => 'ログイン情報が登録されていません',
            ]);
        }
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin') -> logout();

        $request
            -> session() 
            -> invalidate();

        $request
            -> session() 
            -> regenerateToken();

        return view('admin.auth.login');
    }
}
