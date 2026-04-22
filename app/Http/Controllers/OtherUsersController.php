<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OtherUsersController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->where('id', '!=', $request->user()->id)
            ->whereNull('deactivated_at')
            ->with('currentChallenge')
            ->select(['id', 'name', 'username', 'avatar', 'is_public'])
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'is_public' => $user->is_public,
                'challenge_start_date' => optional($user->challenge_start_date)->toDateString(),
                'profile_url' => $user->is_public ? route('public.profile', $user) : null,
            ]);

        return Inertia::render('Users/Index', ['users' => $users]);
    }
}
