<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class RoleMiddlewareTestController extends Controller
{
    public function testMiddleware()
    {
        $user = Auth::user();
        $info = [
            'user' => [
                'id' => $user ? $user->id : null,
                'name' => $user ? $user->name : null,
                'email' => $user ? $user->email : null,
            ],
            'has_roles' => [],
        ];
        
        if ($user && method_exists($user, 'roles')) {
            $info['roles'] = $user->roles->pluck('name')->toArray();
        }
        
        if ($user && method_exists($user, 'hasAnyRole')) {
            $info['has_roles'] = [
                'super-admin' => $user->hasAnyRole(['super-admin']),
                'admin' => $user->hasAnyRole(['admin']),
                'user' => $user->hasAnyRole(['user']),
            ];
        }
        
        return Response::json($info);
    }
}