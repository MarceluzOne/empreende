<?php

namespace App\Mail;

use App\Models\JobSeeker;
use App\Models\JobVacancy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobVacancyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public JobVacancy $vacancy,
        public JobSeeker  $seeker,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nova Vaga: {$this->vacancy->position} — {$this->vacancy->company_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-vacancy',
        );
    }
}
