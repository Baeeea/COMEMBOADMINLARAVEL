<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('id', 'desc')->get();
        return view('faqs', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect()->route('faqs')->with('success', 'FAQ created successfully!');
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('editfaqs', compact('faq'));
    }    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq = Faq::findOrFail($id);
          $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect()->route('faqs')->with('success', 'FAQ updated successfully!');
    }

    public function destroy($id)
    {        $faq = Faq::findOrFail($id);
        $faq->delete();
        return redirect()->route('faqs')->with('success', 'FAQ deleted successfully!');
    }
}
