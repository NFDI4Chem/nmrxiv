<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;


class AnnouncementController extends Controller
{
    /**
     * Show all the list of Announcements created.
     *
     * @param  
     * @return \Pages\Announcement\Index
     */
    public function index(Request $request)
    {
        return Inertia::render('Announcement/Index', [
            'filters' => $request->all('search', 'owner', 'role', 'trashed'),
            'announcements' => Announcement::with('owner')->orderby('created_at', 'DESC')
                ->get()
                ->transform(function ($announcements) {
                    return [
                        'id'         => $announcements->id,
                        'title'      => $announcements->title,
                        'message'    => $announcements->message,
                        'status'     => $announcements->status,
                        'start_time' => $announcements->start_time,
                        'end_time'   => $announcements->end_time,
                        'created_by' => $announcements->owner->first_name,
                    ];
                })
        ]);
    }

    /**
     * Create the new entry for the announcement.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $input = $request->all();
        $user = $request->user();

        //Validating the entries 
        Validator::make($request->all(), [
            'title'      => ['required', 'string', 'max:255'],
            'message'    => ['required'],
            'start_time' => ['required'],
            'end_time'   => ['required'],
        ])->validate();

        //DB transaction
        $announcement = DB::transaction(function () use ($input, $user) {
            return tap(Announcement::create([
                'title'       => $input['title'],
                'message'     => $input['message'],
                'status'      => 'active',
                'start_time'  => $input['start_time'],
                'end_time'    => $input['end_time'],
            ]), function (Announcement $announcement) use ($user) {
                $announcement->owner()->associate($user);
                $announcement->save();
            });
        });
        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('status', 'announcement-created');
    }

    /**
     * Update the specified announcement in the storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\Announcement $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        $input = $request->all();

        Validator::make($input, [
            'title'      => ['required', 'string', 'max:255'],
            'message'    => ['required'],
            'start_time' => ['required'],
            'end_time'   => ['required'],
        ])->validate();

        Announcement::where('id', $input['id'])
            ->update([
                'title'       => $input['title'],
                'message'     => $input['message'],
                'start_time'  => $input['start_time'],
                'end_time'    => $input['end_time'],
            ]);
        $announcement->save();

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('status', 'announcement-updated');
    }

    /**
     * Remove the specified announcement from the storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\Announcement $announcement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Announcement $announcement)
    {
        $announcement->delete();

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('status', 'announcement-deleted');
    }
}
