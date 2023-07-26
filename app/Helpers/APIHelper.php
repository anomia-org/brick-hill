<?php

use Illuminate\Support\Facades\Log;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use Intervention\Image\ImageManagerStatic as Image;

use App\Models\User\User;
use App\Models\User\PastUsername;

function APIParams(array $params)
{
    foreach ($params as $param) {
        if (!request()->has($param)) {
            throw new \App\Exceptions\Custom\InvalidDataException("Missing parameters");
        }
    }
}

function APIExists($val)
{
    if (!$val) {
        throw new ModelNotFoundException;
    }
}

function APIIn($val, array $params)
{
    if (!in_array($val, $params)) {
        throw new \App\Exceptions\Custom\InvalidDataException("Invalid parameter {$val}");
    }
}

function JSONErr($error = 'Error processing request')
{
    return throw new \App\Exceptions\Custom\APIException($error);
}

function JSONSuccess($success = 'success')
{
    return response()->json([
        'success' => $success
    ]);
}

function APIReplace($val, $replace, $with)
{
    if ($val == $replace) {
        return str_replace($val, $replace, $with);
    }
    return $val;
}

function SuspiciousInput($body)
{
    $body = strtolower($body);
    $blockedList = [
        'Location Information',
        'deserved to die',
        'brick luke deez nuts',
        'brick-luke deez nuts',
        'brickluke deez nuts',
        'bldn',
    ];

    foreach ($blockedList as $blocked) {
        $pos = strpos($body, strtolower($blocked));
        if ($pos !== false)
            return true;
    }

    return false;
}

function UsernameValidator($username, $user_id = 0)
{
    $alnumUsername = str_replace(array('-', '_', '.', ' '), '', $username);
    $errors = [];
    if (substr($username, -1) == ' ' || substr($username, 0, 1) == ' ') {
        $errors[] = 'You cannot include a space at the beginning or end of your username.';
    }
    if (strlen($username) < 3 || strlen($username) > 26 || $username != ctype_alnum($alnumUsername)) {
        $errors[] = 'Username must be 3-26 alphanumeric characters (including [ , ., -, _]).';
    }
    if (strpos($username, '  ') !== false || strpos($username, '..') !== false || strpos($username, '--') !== false || strpos($username, '__') !== false) {
        $errors[] = 'Spaces, periods, hyphens and underscores must be separated.';
    }

    $badWords = [
        'fuck',
        'bitch',
    ];
    foreach ($badWords as $w) {
        if (stripos($username, $w) !== false)
            $errors[] = 'Username has a restricted word in it';
    }

    if ($user_id == 0) {
        $count = PastUsername::oldName($username)->count();
        if ($count > 0) {
            $errors[] = 'Username already taken';
        }
    } else {
        $count = PastUsername::oldName($username)->where('user_id', '!=', $user_id)->count();
        if ($count > 0) {
            $errors[] = 'Username already taken';
        }
    }
    $rules = [
        'username' => 'required|string|min:3|max:26|unique:users',
    ];
    $validator = validator(['username' => $username], $rules);

    if ($validator->fails())
        $errors[] = $validator->messages()->first();

    if (count($errors) > 0 && $errors[0] == "The username has already been taken." && $user_id !== 0) {
        $user = User::where('username', $username)->first();
        if ($user->id == $user_id)
            $errors = [];
    }

    if (!empty($errors))
        return $errors[0];
    return true;
}

// NOT AN API FUNCTION BUT DOESNT WORK IN Helper.php

function clampNum($num, $min, $max)
{
    return max($min, min($max, $num));
}

function validation($rules, $route = false)
{
    $validator = validator(request()->all(), $rules);
    if ($validator->fails()) {
        if ($route) {
            $redir = redirect($route)
                ->withInput()
                ->withErrors(['errors' => $validator->messages()->first()]);
        } else {
            $redir = back()
                ->withInput()
                ->withErrors(['errors' => $validator->messages()->first()]);
        }

        \Session::driver()->save();
        $redir->send();
    }
}

function itemImg($hash)
{
    return config('site.storage.img') . $hash . '.png';
}

function error($error, $route = false)
{
    if ($route) {
        return redirect($route)
            ->withInput()
            ->withErrors($error);
    } else {
        return back()
            ->withInput()
            ->withErrors($error);
    }
}

function success($msg, $route = false)
{
    if ($route) {
        return redirect($route)
            ->with('success', $msg);
    } else {
        return back()
            ->with('success', $msg);
    }
}
