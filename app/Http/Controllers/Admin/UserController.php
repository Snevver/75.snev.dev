<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Users', [
            'users' => User::query()->latest()->paginate(30),
        ]);
    }

    public function toggleAdmin(User $user)
    {
        $user->is_admin = ! $user->is_admin;
        $user->save();

        return back()->with('success', 'User role updated.');
    }

    public function deactivate(User $user)
    {
        $user->deactivated_at = now();
        $user->save();

        return back()->with('success', 'User deactivated.');
    }
}
