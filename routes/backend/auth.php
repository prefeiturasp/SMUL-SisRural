<?php

use App\Http\Controllers\Backend\Auth\Role\RoleController;
use App\Http\Controllers\Backend\Auth\User\UserController;
use App\Http\Controllers\Backend\Auth\User\UserSocialController;
use App\Http\Controllers\Backend\Auth\User\UserStatusController;
use App\Http\Controllers\Backend\Auth\User\UserSessionController;
use App\Http\Controllers\Backend\Auth\User\UserPasswordController;
use App\Http\Controllers\Backend\Auth\User\UserConfirmationController;

// All route names are prefixed with 'admin.auth'.
Route::group([
    'prefix' => 'auth',
    'as' => 'auth.',
    'namespace' => 'Auth',
], function () {
    // User Management
    Route::group(['namespace' => 'User'], function () {
        // User Status'
        Route::get('user/deactivated', [UserStatusController::class, 'getDeactivated'])->middleware('permission:view menu users')->name('user.deactivated');
        Route::get('user/deleted', [UserStatusController::class, 'getDeleted'])->middleware('permission:view menu users')->name('user.deleted');

        Route::post('user/change_role/{userId}', [UserController::class, 'changeRole'])->middleware('permission:view menu users')->name('user.change_role');

        // User CRUD
        Route::get('user', [UserController::class, 'index'])->middleware('permission:view menu users')->name('user.index');
        Route::get('user/datatable', [UserController::class, 'datatable'])->middleware('permission:view menu users')->name('user.datatable');
        Route::get('user/create', [UserController::class, 'create'])->middleware('can:create,App\Models\Auth\User')->name('user.create');
        Route::post('user', [UserController::class, 'store'])->middleware('can:create,App\Models\Auth\User')->name('user.store');


        Route::get('user/all', [UserController::class, 'indexAll'])->middleware('permission:view menu users')->name('user.indexAll');
        Route::get('user/datatableAll', [UserController::class, 'datatableAll'])->middleware('permission:view menu users')->name('user.datatableAll');
        Route::get('/user/{userAll}/roles/create/userAll', [UserController::class, 'rolesCreateUserAll'])->name('user.roles.create.userAll');
        Route::post('/user/{userAll}/roles/store/userAll', [UserController::class, 'rolesStoreUserAll'])->name('user.roles.store.userAll');

        // Specific User
        Route::group(['prefix' => 'user/{user}'], function () {
            // User
            Route::get('/', [UserController::class, 'show'])->middleware('can:view,user')->name('user.show');
            Route::get('edit', [UserController::class, 'edit'])->middleware('can:update,user')->name('user.edit');

            Route::group(['prefix' => '/roles'], function () {
                Route::get('/', [UserController::class, 'rolesIndex'])->name('user.roles.index');
                Route::get('/datatable', [UserController::class, 'rolesDatatable'])->name('user.roles.datatable');
                Route::get('/create', [UserController::class, 'rolesCreate'])->name('user.roles.create');
                Route::post('/store', [UserController::class, 'rolesStore'])->name('user.roles.store');
                Route::delete('/delete/{userRole}', [UserController::class, 'rolesDestroy'])->name('user.roles.destroy');
                Route::get('/edit/{userRole}', [UserController::class, 'rolesEdit'])->name('user.roles.edit');
                Route::post('/update/{userRole}', [UserController::class, 'rolesUpdate'])->name('user.roles.update');
            });

            Route::patch('/', [UserController::class, 'update'])->middleware('can:update,user')->name('user.update');
            Route::delete('/', [UserController::class, 'destroy'])->middleware('can:delete,user')->name('user.destroy');

            // Account
            Route::get('account/confirm/resend', [UserConfirmationController::class, 'sendConfirmationEmail'])->name('user.account.confirm.resend');

            // Status
            Route::get('mark/{status}', [UserStatusController::class, 'mark'])->middleware('can:update,user')->name('user.mark')->where(['status' => '[0,1]']);

            // Social
            Route::delete('social/{social}/unlink', [UserSocialController::class, 'unlink'])->name('user.social.unlink');

            // Confirmation
            Route::get('confirm', [UserConfirmationController::class, 'confirm'])->name('user.confirm');
            Route::get('unconfirm', [UserConfirmationController::class, 'unconfirm'])->name('user.unconfirm');

            // Password
            Route::get('password/change', [UserPasswordController::class, 'edit'])->middleware('can:update,user')->name('user.change-password');
            Route::patch('password/change', [UserPasswordController::class, 'update'])->middleware('can:update,user')->name('user.change-password.post');

            // Session
            Route::get('clear-session', [UserSessionController::class, 'clearSession'])->name('user.clear-session');

            // Deleted
            Route::get('delete', [UserStatusController::class, 'delete'])->middleware('can:delete,user')->name('user.delete-permanently');
            Route::get('restore', [UserStatusController::class, 'restore'])->middleware('can:delete,user')->name('user.restore');
        });
    });

});
