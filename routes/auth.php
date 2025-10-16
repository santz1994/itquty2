<?php

/**
 * Authentication Routes
 * 
 * This file contains all authentication-related routes:
 * - Login/Logout
 * - Password Reset
 * - Session Management
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Login Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/home');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

// Logout Route
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Password Reset Routes
Route::get('/password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Session Extension Route for AJAX requests
Route::post('/extend-session', function (\Illuminate\Http\Request $request) {
    if ($request->ajax() && Auth::check()) {
        $request->session()->put('last_activity', time());
        return response()->json(['status' => 'success', 'message' => 'Session extended']);
    }
    return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
})->middleware('auth')->name('extend-session');

// Force re-login route to clear session (Local env only for debugging)
if (app()->environment('local')) {
    Route::get('/force-relogin', function() {
        Auth::logout();
        session()->flush();
        session()->regenerate();
        return redirect('/login')->with('message', '<strong>Session cleared!</strong> Please login again to refresh your roles.');
    });
}
