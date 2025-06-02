<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderBy('createdAt', 'desc')->get();
        return view('announcements', compact('announcements'));
    }public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'announcement_date' => 'required|date',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'title' => $request->title,
            'content' => $request->description,
            'createdAt' => $request->announcement_date
        ];

        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $filename = time() . '_' . $picture->getClientOriginalName();
            $picture->move(public_path('uploads/announcements'), $filename);
            $data['image'] = $filename;
        }        Announcement::create($data);

        return redirect()->route('announcements')->with('success', 'Announcement created successfully!');
    }public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('editannouncement', compact('announcement'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'announcement_date' => 'required|date',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $announcement = Announcement::findOrFail($id);

        $data = [
            'title' => $request->title,
            'content' => $request->description,
            'createdAt' => $request->announcement_date
        ];

        if ($request->hasFile('picture')) {
            // Delete old picture if exists
            if ($announcement->image && file_exists(public_path('uploads/announcements/' . $announcement->image))) {
                unlink(public_path('uploads/announcements/' . $announcement->image));
            }

            $picture = $request->file('picture');
            $filename = time() . '_' . $picture->getClientOriginalName();
            $picture->move(public_path('uploads/announcements'), $filename);
            $data['image'] = $filename;
        }        $announcement->update($data);

        return redirect()->route('announcements')->with('success', 'Announcement updated successfully!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        // Delete the picture file if it exists
        if ($announcement->image && file_exists(public_path('uploads/announcements/' . $announcement->image))) {
            unlink(public_path('uploads/announcements/' . $announcement->image));
        }
          $announcement->delete();

        return redirect()->route('announcements')->with('success', 'Announcement deleted successfully!');
    }
}
