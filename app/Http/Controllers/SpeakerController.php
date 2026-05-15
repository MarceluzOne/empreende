<?php

namespace App\Http\Controllers;

use App\Models\Speaker;
use Illuminate\Http\Request;

class SpeakerController extends Controller
{
    public function index()
    {
        $speakers = Speaker::withCount('events')->orderBy('name')->paginate(15);
        return view('speakers.index', compact('speakers'));
    }

    public function create()
    {
        return view('speakers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'bio'   => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Speaker::create($request->only(['name', 'bio', 'email', 'phone']));

        return redirect()->route('speakers.index')->with('success', 'Palestrante cadastrado com sucesso!');
    }
}
