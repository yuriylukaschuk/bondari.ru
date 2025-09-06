<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{

    private string $dashboard = 'register';
    private string $dashboard_title = 'Регистрация пользователей';
    private string $err = '';

    private function getRoles()
    {
        return Role::all()->map(function($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ];
        });
    }

    private function getPermissions()
    {
        return Permission::all()->map(function($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
            ];
        });
    }

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Dashboard', [
            'dashboard' => $this->dashboard,
            'dashboard_title' => $this->dashboard_title,
            'components' => [
                'roles' => $this->getRoles(),
                'permissions' => $this->getPermissions()
            ],
            'err' => $this->err,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->roles()->attach($request->role_id);
        $user->permissions()->attach($request->permissions);

        return Inertia::render('Dashboard', [
            'dashboard' => $this->dashboard,
            'dashboard_title' => $this->dashboard_title,
            'components' => [
                'roles' => $this->getRoles(),
                'permissions' => $this->getPermissions()
            ],
            'err' => $this->err,
        ]);
    }
}
