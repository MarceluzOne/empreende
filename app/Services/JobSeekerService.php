<?php

namespace App\Services;

use App\Models\JobSeeker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobSeekerService
{
    public function store(array $data, ?UploadedFile $curriculo = null): JobSeeker
    {
        $data['user_id'] = Auth::id();
        $data['cpf']     = preg_replace('/[^0-9]/', '', $data['cpf'] ?? '');
        $data['phone']   = $data['phone'] ? preg_replace('/[^0-9]/', '', $data['phone']) : null;

        if ($curriculo) {
            $data['curriculo_path'] = $curriculo->store('curriculos', 'public');
        }

        return JobSeeker::create($data);
    }

    public function update(JobSeeker $seeker, array $data, ?UploadedFile $curriculo = null): JobSeeker
    {
        $data['cpf']   = preg_replace('/[^0-9]/', '', $data['cpf'] ?? '');
        $data['phone'] = $data['phone'] ? preg_replace('/[^0-9]/', '', $data['phone']) : null;

        if ($curriculo) {
            if ($seeker->curriculo_path) {
                Storage::disk('public')->delete($seeker->curriculo_path);
            }
            $data['curriculo_path'] = $curriculo->store('curriculos', 'public');
        }

        $seeker->update($data);
        return $seeker;
    }

    public function destroy(JobSeeker $seeker): void
    {
        if ($seeker->curriculo_path) {
            Storage::disk('public')->delete($seeker->curriculo_path);
        }
        $seeker->delete();
    }
}
