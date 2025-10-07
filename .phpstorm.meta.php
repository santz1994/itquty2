<?php

namespace PHPSTORM_META {

    // Tell PhpStorm about Spatie Laravel Permission methods on User model
    override(\App\User::class, map([
        'hasRole' => '@',
        'hasAnyRole' => '@', 
        'hasAllRoles' => '@',
        'getRoleNames' => '@',
        'assignRole' => '@',
        'removeRole' => '@',
        'syncRoles' => '@',
        'hasPermissionTo' => '@',
        'hasAnyPermission' => '@',
        'hasAllPermissions' => '@',
        'givePermissionTo' => '@',
        'revokePermissionTo' => '@',
        'syncPermissions' => '@',
    ]));

    // Define return types for common Laravel Permission methods
    expectedReturnValues(\App\User::hasRole(), true, false);
    expectedReturnValues(\App\User::hasAnyRole(), true, false);
    expectedReturnValues(\App\User::hasAllRoles(), true, false);
    expectedReturnValues(\App\User::hasPermissionTo(), true, false);
    expectedReturnValues(\App\User::hasAnyPermission(), true, false);
    expectedReturnValues(\App\User::hasAllPermissions(), true, false);

    // Auth::user() with role methods
    registerArgumentsSet('user_roles', 'admin', 'super-admin', 'user', 'management');
    expectedArguments(\App\User::hasRole(), 0, argumentsSet('user_roles'));
    expectedArguments(\App\User::hasAnyRole(), 0, argumentsSet('user_roles'));
    expectedArguments(\App\User::hasAllRoles(), 0, argumentsSet('user_roles'));
    expectedArguments(\App\User::assignRole(), 0, argumentsSet('user_roles'));
    expectedArguments(\App\User::removeRole(), 0, argumentsSet('user_roles'));
    expectedArguments(\App\User::syncRoles(), 0, argumentsSet('user_roles'));
}