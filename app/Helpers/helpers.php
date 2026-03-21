<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('authUser')) {
    function authUser()
    {
        return Auth::guard('sanctum')->user();
    }
}

if (!function_exists('authUserId')) {
    function authUserId()
    {
        return Auth::guard('sanctum')->id();
    }
}

if (!function_exists('routePrefix')) {
    function routePrefix()
    {
        return request()->route()->routePrefix;
    }
}