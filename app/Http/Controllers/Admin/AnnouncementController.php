<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\WhatsNewNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Pages\Announcement\Index;

class AnnouncementController extends Controller
{
    /**
     * Show all the list of Announcements created.
     *
     * @return Index
     */
    public function index(Request $request)
    {
        return Inertia::render('Announcement/Index', [
            'announcements' => Announcement::with('owner')->orderby('created_at', 'DESC')
                ->when($request->input('search'), function ($query, $search) {
                    $query->where('message', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                })
                ->get()
                ->transform(function ($announcements) {
                    return [
                        'id' => $announcements->id,
                        'title' => $announcements->title,
                        'message' => $announcements->message,
                        'type' => $announcements->type,
                        'release_version' => $announcements->release_version,
                        'release_notes' => $announcements->release_notes,
                        'status' => $announcements->status,
                        'start_time' => $announcements->start_time,
                        'end_time' => $announcements->end_time,
                        'created_by' => $announcements->owner->first_name,
                    ];
                }),
        ]);
    }

    /**
     * Create the new entry for the announcement.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $input = $request->all();
        $user = $request->user();
        $sendWhatsNewNotification = $request->boolean('send_whats_new_notification');
        $releaseVersion = $request->filled('release_version') ? $input['release_version'] : null;
        $releaseNotes = $request->filled('release_notes') ? $input['release_notes'] : null;

        if ($sendWhatsNewNotification) {
            $input['status'] = 'inactive';
        } elseif ($input['enabled']) {
            $input['status'] = 'active';
        } else {
            $input['status'] = 'inactive';
        }
        // Validating the entries
        Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'release_version' => ['nullable', 'string', 'max:100'],
            'release_notes' => ['nullable', 'string'],
        ])->validate();

        // DB transaction
        $announcement = DB::transaction(function () use ($input, $user, $sendWhatsNewNotification, $releaseVersion, $releaseNotes) {
            return tap(Announcement::create([
                'title' => $input['title'],
                'message' => $input['message'],
                'type' => $sendWhatsNewNotification ? 'whats_new' : 'announcement',
                'release_version' => $releaseVersion,
                'release_notes' => $releaseNotes,
                'status' => $input['status'],
                'start_time' => $input['start_time'],
                'end_time' => $input['end_time'],
            ]), function (Announcement $announcement) use ($user) {
                $announcement->owner()->associate($user);
                $announcement->save();
            });
        });

        if ($sendWhatsNewNotification) {
            $details = [
                'release_version' => $releaseVersion,
                'release_notes' => $releaseNotes ?? $input['message'],
            ];

            User::query()->chunkById(500, function ($users) use ($announcement, $details) {
                Notification::send($users, new WhatsNewNotification($announcement, $details));
            });
        }

        return $request->wantsJson() ? new JsonResponse("{'success': 'Announcement created successfully'}", 200) : redirect()->route('console.announcements')->with('success', 'Announcement created successfully');
    }

    /**
     * Update the specified announcement in the storage.
     *
     * @return Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        $input = $request->all();
        $sendWhatsNewNotification = $request->boolean('send_whats_new_notification');
        $releaseVersion = $request->filled('release_version') ? $input['release_version'] : null;
        $releaseNotes = $request->filled('release_notes') ? $input['release_notes'] : null;

        if ($sendWhatsNewNotification) {
            $input['status'] = 'inactive';
        } elseif ($input['enabled']) {
            $input['status'] = 'active';
        } else {
            $input['status'] = 'inactive';
        }
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'release_version' => ['nullable', 'string', 'max:100'],
            'release_notes' => ['nullable', 'string'],
        ])->validate();

        Announcement::where('id', $input['id'])
            ->update([
                'title' => $input['title'],
                'message' => $input['message'],
                'type' => $sendWhatsNewNotification ? 'whats_new' : 'announcement',
                'release_version' => $releaseVersion,
                'release_notes' => $releaseNotes,
                'status' => $input['status'],
                'start_time' => $input['start_time'],
                'end_time' => $input['end_time'],
            ]);
        $announcement->save();

        if ($sendWhatsNewNotification) {
            $details = [
                'release_version' => $releaseVersion,
                'release_notes' => $releaseNotes ?? $input['message'],
            ];

            User::query()->chunkById(500, function ($users) use ($announcement, $details) {
                Notification::send($users, new WhatsNewNotification($announcement, $details));
            });
        }

        return $request->wantsJson() ? new JsonResponse("{'success': 'Announcement updated successfully'}", 200) : redirect()->route('console.announcements')->with('success', 'Announcement updated successfully');
    }

    /**
     * Remove the specified announcement from the storage.
     *
     * @return Response
     */
    public function destroy(Request $request, Announcement $announcement)
    {
        $announcement->delete();

        return $request->wantsJson() ? new JsonResponse("{'success': 'Announcement deleted successfully'}", 200) : redirect()->route('console.announcements')->with('success', 'Announcement deleted successfully');
    }
}
