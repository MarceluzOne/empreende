<?php

namespace App\Http\Controllers;

use App\Models\Speaker;
use App\Rules\Cpf;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        $this->normalizeCpf($request);

        $request->validate([
            'name'      => 'required|string|max:255',
            'bio'       => 'nullable|string',
            'email'     => 'nullable|email|max:255',
            'phone'     => 'nullable|string|max:20',
            'cpf'       => ['required', 'string', 'max:14', new Cpf, Rule::unique('speakers', 'cpf')],
            'photo'       => 'nullable|image|max:4096',
            'signature'   => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'is_director' => 'nullable|boolean',
        ], $this->cpfMessages());

        $data = $request->only(['name', 'bio', 'email', 'phone', 'cpf']);

        // Quem é o diretor só o administrador define; o campo nem aparece para
        // funcionário, e ignorá-lo aqui impede que um POST forjado o marque.
        $data['is_director'] = auth()->user()->isAdmin() && $request->boolean('is_director');

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storePhoto($request->file('photo'));
        }
        if ($request->hasFile('signature')) {
            $data['signature_path'] = $this->storeSignature($request->file('signature'));
        }

        $speaker = Speaker::create($data);
        $this->syncDirectorFlag($speaker);
        AuditService::log('created', $speaker);

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
        $this->normalizeCpf($request);

        $request->validate([
            'name'      => 'required|string|max:255',
            'bio'       => 'nullable|string',
            'email'     => 'nullable|email|max:255',
            'phone'     => 'nullable|string|max:20',
            'cpf'       => ['required', 'string', 'max:14', new Cpf, Rule::unique('speakers', 'cpf')->ignore($speaker->getKey())],
            'photo'       => 'nullable|image|max:4096',
            'signature'   => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'is_director' => 'nullable|boolean',
        ], $this->cpfMessages());

        $data = $request->only(['name', 'bio', 'email', 'phone', 'cpf']);

        // Fora do administrador o campo não é enviado: mantém o valor atual, para
        // que um funcionário editando o diretor não desmarque a flag sem querer.
        if (auth()->user()->isAdmin()) {
            $data['is_director'] = $request->boolean('is_director');
        }

        if ($request->hasFile('photo')) {
            if ($speaker->photo_path) {
                Storage::disk('public')->delete($speaker->photo_path);
            }
            $data['photo_path'] = $this->storePhoto($request->file('photo'));
        }
        if ($request->hasFile('signature')) {
            if ($speaker->signature_path) {
                Storage::disk('public')->delete($speaker->signature_path);
            }
            $data['signature_path'] = $this->storeSignature($request->file('signature'));
        }

        $speaker->update($data);
        $this->syncDirectorFlag($speaker);
        AuditService::log('updated', $speaker);

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
        if ($speaker->signature_path) {
            Storage::disk('public')->delete($speaker->signature_path);
        }
        AuditService::log('deleted', $speaker);
        $speaker->delete();

        return redirect()->route('speakers.index')->with('success', 'Palestrante removido com sucesso!');
    }

    public function quickStore(Request $request)
    {
        $this->normalizeCpf($request);

        $request->validate([
            'name'        => 'required|string|max:255',
            'bio'         => 'nullable|string',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:20',
            'cpf'         => ['required', 'string', 'max:14', new Cpf, Rule::unique('speakers', 'cpf')],
            'photo'       => 'nullable|image|max:4096',
            'signature'   => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'is_director' => 'nullable|boolean',
        ], $this->cpfMessages());

        $data = $request->only(['name', 'bio', 'email', 'phone', 'cpf']);

        // Mesma regra do cadastro completo: só administrador define o diretor.
        $data['is_director'] = auth()->user()->isAdmin() && $request->boolean('is_director');

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storePhoto($request->file('photo'));
        }
        if ($request->hasFile('signature')) {
            $data['signature_path'] = $this->storeSignature($request->file('signature'));
        }

        $speaker = Speaker::create($data);
        $this->syncDirectorFlag($speaker);
        AuditService::log('created', $speaker);

        return response()->json([
            'id'   => $speaker->id,
            'name' => $speaker->name,
        ], 201);
    }

    /**
     * Tira a máscara do CPF antes de validar, para que `unique` compare com o
     * formato guardado no banco (só dígitos).
     */
    private function normalizeCpf(Request $request): void
    {
        $request->merge([
            'cpf' => preg_replace('/\D/', '', (string) $request->input('cpf')),
        ]);
    }

    private function cpfMessages(): array
    {
        // Sem lang/pt_BR/validation.php as mensagens padrão sairiam em inglês.
        return [
            'cpf.required' => 'Informe o CPF do palestrante.',
            'cpf.unique'   => 'Já existe um palestrante cadastrado com este CPF.',
        ];
    }

    /**
     * Garante um único diretor: ao marcar um palestrante, desmarca os demais.
     */
    private function syncDirectorFlag(Speaker $speaker): void
    {
        if ($speaker->is_director) {
            Speaker::director()->whereKeyNot($speaker->getKey())->update(['is_director' => false]);
        }
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

    /**
     * Guarda a assinatura como veio (sem cortar/redimensionar), para preservar
     * a transparência do PNG.
     */
    private function storeSignature(\Illuminate\Http\UploadedFile $file): string
    {
        $ext      = strtolower($file->getClientOriginalExtension() ?: 'png');
        $filename = 'signatures/' . Str::uuid() . '.' . $ext;

        Storage::disk('public')->putFileAs('signatures', $file, basename($filename));

        return $filename;
    }
}
