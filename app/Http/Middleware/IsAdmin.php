<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;

class IsAdmin {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {

		if (Auth::guard()->check()) {
			if ((Auth::guard()->user()->is_admin == 1)) {
				return $next($request);
			} elseif (!Auth::guard()->user()->is_admin) {
				Auth::guard()->logout();
				Session::flash('msg', ['status' => 'danger', 'msgs' => 'You are not Authenticated']);
				return redirect()->route('login');
			}

		}
		Session::flash('msg', ['status' => 'danger', 'msgs' => 'You are not Authenticated']);
		return redirect()->route('login');
	}
}
