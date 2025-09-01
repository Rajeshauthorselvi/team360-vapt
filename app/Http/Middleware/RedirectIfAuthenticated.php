<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Session;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guard)
    {
        $path_url = $request->path();
        $url = explode('/', $path_url);
        $guards = empty($guards) ? [null] : $guards;
        if (count(array_filter($url)) == 0 && Session::has('survey_url') && Auth::check() && Auth::id() > 1) {
            return Redirect::route('user.dashboard', Session::get('survey_url'));
        }
        if (Auth::guard($guard)->check()) {
            if (Auth::check() && Auth::id() == 1) {
                return Redirect::route('admin.dashboard');
            }
            if (Auth::check() && Auth::id() > 1) {
                return Redirect::route('user.dashboard', $url[0]);
            }
        }

        return $next($request);

    }
}
