<?php

namespace App\Services;

use App\Models\JobSeeker;
use Illuminate\Support\Facades\Auth;

class JobSeekerService
{
    public function store(array $data): JobSeeker
    {
        $data['user_id'] = Auth::id();
        $data['phone']   = isset($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null;

        return JobSeeker::create($data);
    }

    public function update(JobSeeker $seeker, array $data): JobSeeker
    {
        $data['phone'] = isset($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null;

        $seeker->update($data);
        return $seeker;
    }

    public function destroy(JobSeeker $seeker): void
    {
        $seeker->delete();
    }
}
