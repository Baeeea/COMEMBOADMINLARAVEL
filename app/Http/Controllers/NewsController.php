<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    private function triggerLiveUpdate()
    {
        Cache::put('last_database_update', time(), 3600);
    }

    public function index()
    {
        $news = News::orderBy('createdAt', 'desc')->get();
        
        // Debug: Check if we have data
        if ($news->isEmpty()) {
            \Log::info('No news found in database');
        } else {
            \Log::info('Found ' . $news->count() . ' news items');
        }
        
        return view('news', compact('news'));
    }

    public function show($id)
    {
        $news = News::findOrFail($id);
        return view('shownews', compact('news'));
    }

    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('editnews', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);
        
        $request->validate([
            'Title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'Title' => $request->Title,
            'content' => $request->content,
            'createdAt' => $request->createdAt ?? $news->createdAt
        ];

        if ($request->hasFile('image')) {
            // Handle image upload if needed
            $imagePath = $request->file('image')->store('news_images', 'public');
            $data['image'] = $imagePath;
        }        $news->update($data);

        $this->triggerLiveUpdate();
        return redirect()->route('news')->with('success', 'News updated successfully!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Title' => 'required|string|max:255',
            'content' => 'required|string',
            'createdAt' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'Title' => $request->Title,
            'content' => $request->content,
            'createdAt' => $request->createdAt
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news_images', 'public');
            $data['image'] = $imagePath;
        }        News::create($data);

        $this->triggerLiveUpdate();
        return redirect()->route('news')->with('success', 'News created successfully!');
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        
        // Delete image if exists
        if ($news->image && \Storage::exists('public/' . $news->image)) {
            \Storage::delete('public/' . $news->image);
        }
        
        $news->delete();

        $this->triggerLiveUpdate();
        return redirect()->route('news')->with('success', 'News deleted successfully!');
    }
}
