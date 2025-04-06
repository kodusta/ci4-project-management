<?php

if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        return session()->get('logged_in') === true;
    }
}

if (!function_exists('get_user_id')) {
    function get_user_id()
    {
        return session()->get('user_id');
    }
}

if (!function_exists('get_username')) {
    function get_username()
    {
        return session()->get('username');
    }
}

if (!function_exists('get_user_email')) {
    function get_user_email()
    {
        return session()->get('email');
    }
}

if (!function_exists('require_login')) {
    function require_login()
    {
        if (!is_logged_in()) {
            return redirect()->to('/login');
        }
    }
}
