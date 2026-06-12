<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidaturaAceitaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public JobApplication $application) {}

    public function envelope(): Envelope
    {
        $position = $this->application->vacancy->position;
        $company  = $this->application->vacancy->company_name;

        return new Envelope(
            subject: "Parabéns! Você foi aceito para {$position} — {$company}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.candidatura-aceita',
        );
    }
}
