<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class RoleMiddlewareTestController extends Controller
{
    public function testMiddleware()
    {
    /** @var \App\User $user */
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
            $info['has_roles'] = [];
            $hasAnyRoleMethod = 'hasAnyRole';
            $info['has_roles']['super-admin'] = (bool) $user->$hasAnyRoleMethod(['super-admin']);
            $info['has_roles']['admin'] = (bool) $user->$hasAnyRoleMethod(['admin']);
            $info['has_roles']['user'] = (bool) $user->$hasAnyRoleMethod(['user']);
        }
        
        return Response::json($info);
    }
}