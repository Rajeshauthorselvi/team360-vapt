<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $path_url = $request->path();
        $url = explode('/', $path_url);

        if (count(array_filter($url)) == 0 && Session::has('survey_url') && Auth::check() && Auth::id() > 1) {
            return Redirect::route('user.dashboard', Session::get('survey_url'));
        }
        if (Auth::check()) {
            if (Auth::check() && Auth::id() == 1) {
                return Redirect::route('admin.dashboard');
            }

            if (Auth::check() && Auth::id() > 1) {
                return Redirect::route('user.dashboard', $url[0]);
            }

        }
        if ($url[0] != 'login') {
            return url($url[0].'/login');
        }

        // if (!$request->expectsJson()) {
        //     return route('login');
        // }
    }
}
