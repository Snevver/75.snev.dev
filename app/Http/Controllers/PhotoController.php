<?php

namespace App\Http\Controllers;

use App\Models\ProgressPhoto;
use App\Services\ChallengeService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PhotoController extends Controller
{
    public function index(Request $request): Response
    {
        $photos = $request->user()->progressPhotos()->latest('taken_at')->get()->map(fn(ProgressPhoto $photo) => [
            'id' => $photo->id,
            'taken_at' => optional($photo->taken_at)->toDateString(),
            'url' => route('photos.show', $photo),
        ]);

        return Inertia::render('Photos/Index', [
            // Keep `data` for older built bundles that still read photos.data
            'photos' => [
                'data' => $photos,
            ],
        ]);
    }

    public function store(Request $request, ChallengeService $challengeService)
    {
        $data = $request->validate([
            'photo' => ['required', 'image', 'max:8192'],
            'log_date' => ['nullable', 'date'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $date = CarbonImmutable::parse($data['log_date'] ?? now(), $user->timezone)->startOfDay();
        $log = $challengeService->logForDate($user, $date);
        $path = $data['photo']->store('progress-photos/' . $user->id, 'local');

        $existingPhoto = ProgressPhoto::query()
            ->where('user_id', $user->id)
            ->where('daily_log_id', $log->id)
            ->first();

        if ($existingPhoto?->file_path && Storage::disk('local')->exists($existingPhoto->file_path)) {
            Storage::disk('local')->delete($existingPhoto->file_path);
        }

        $photo = ProgressPhoto::updateOrCreate(
            ['user_id' => $user->id, 'daily_log_id' => $log->id],
            ['file_path' => $path, 'taken_at' => now(), 'is_public' => $data['is_public'] ?? false]
        );

        return back()->with('success', 'Photo uploaded.')->with('photoUrl', route('photos.show', $photo));
    }

    public function destroy(ProgressPhoto $photo, Request $request)
    {
        abort_unless($photo->user_id === $request->user()->id, 403);

        if ($photo->file_path && Storage::disk('local')->exists($photo->file_path)) {
            Storage::disk('local')->delete($photo->file_path);
        }

        $photo->delete();

        return back()->with('success', 'Photo deleted.');
    }

    public function show(ProgressPhoto $photo, Request $request): StreamedResponse
    {
        abort_unless(
            $photo->user_id === $request->user()?->id ||
                ($photo->user->is_public && $photo->is_public && $photo->user->share_public_photos),
            403
        );

        return Storage::disk('local')->response($photo->file_path);
    }
}
