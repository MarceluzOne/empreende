<?php

namespace App\Http\Controllers;

use App\Models\Speaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'photo' => 'nullable|image|max:4096',
        ]);

        $data = $request->only(['name', 'bio', 'email', 'phone']);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storePhoto($request->file('photo'));
        }

        Speaker::create($data);

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo && str_starts_with($redirectTo, url('/'))) {
            return redirect($redirectTo)->with('success', 'Palestrante cadastrado com sucesso!');
        }

        return redirect()->route('speakers.index')->with('success', 'Palestrante cadastrado com sucesso!');
    }

    public function edit(Speaker $speaker)
    {
        return view('speakers.edit', compact('speaker'));
    }

    public function update(Request $request, Speaker $speaker)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'bio'   => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:4096',
        ]);

        $data = $request->only(['name', 'bio', 'email', 'phone']);

        if ($request->hasFile('photo')) {
            if ($speaker->photo_path) {
                Storage::disk('public')->delete($speaker->photo_path);
            }
            $data['photo_path'] = $this->storePhoto($request->file('photo'));
        }

        $speaker->update($data);

        return redirect()->route('speakers.index')->with('success', 'Palestrante atualizado com sucesso!');
    }

    public function destroy(Speaker $speaker)
    {
        if ($speaker->events()->exists()) {
            return back()->with('error', 'Não é possível excluir: este palestrante está vinculado a um ou mais eventos. Troque ou remova o palestrante desses eventos antes.');
        }

        if ($speaker->photo_path) {
            Storage::disk('public')->delete($speaker->photo_path);
        }
        $speaker->delete();

        return redirect()->route('speakers.index')->with('success', 'Palestrante removido com sucesso!');
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'bio'   => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $speaker = Speaker::create($validated);

        return response()->json([
            'id'   => $speaker->id,
            'name' => $speaker->name,
        ], 201);
    }

    private function storePhoto(\Illuminate\Http\UploadedFile $file): string
    {
        $size = 400;
        $mime = $file->getMimeType();

        $src = match (true) {
            str_contains($mime, 'png')  => imagecreatefrompng($file->getRealPath()),
            str_contains($mime, 'webp') => imagecreatefromwebp($file->getRealPath()),
            default                     => imagecreatefromjpeg($file->getRealPath()),
        };

        $w    = imagesx($src);
        $h    = imagesy($src);
        $side = min($w, $h);
        $x    = (int)(($w - $side) / 2);
        $y    = (int)(($h - $side) / 2);

        $dst = imagecreatetruecolor($size, $size);
        imagecopyresampled($dst, $src, 0, 0, $x, $y, $size, $size, $side, $side);
        imagedestroy($src);

        $filename = 'speakers/' . Str::uuid() . '.webp';
        Storage::disk('public')->makeDirectory('speakers');
        imagewebp($dst, Storage::disk('public')->path($filename), 85);
        imagedestroy($dst);

        return $filename;
    }
}
