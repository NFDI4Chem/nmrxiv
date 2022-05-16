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
            'announcements' => Announcement::with('owner')->orderby('created_at', 'DESC')
                ->when($request->input('search'), function($query, $search) {
                    $query->where('message', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
                })
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

        if($input['enabled'])
            $input['status'] = 'active';
        else
            $input['status'] = 'inactive';
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
                'status'      => $input['status'],
                'start_time'  => $input['start_time'],
                'end_time'    => $input['end_time'],
            ]), function (Announcement $announcement) use ($user) {
                $announcement->owner()->associate($user);
                $announcement->save();
            });
        });
        return redirect()->route('announcements')->with('success', 'Announcement created successfully');
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
        if($input['enabled'])
            $input['status'] = 'active';
        else
            $input['status'] = 'inactive';
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
                'status'      => $input['status'],
                'start_time'  => $input['start_time'],
                'end_time'    => $input['end_time'],
            ]);
        $announcement->save();

        return redirect()->route('announcements')->with('success', 'Announcement updated successfully');
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

        return redirect()->route('announcements')->with('success', 'Announcement deleted successfully');
    }
}
