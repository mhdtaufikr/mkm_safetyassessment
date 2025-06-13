<?php

namespace App\Mail;

use App\Models\RiskAssessment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RiskAssessmentSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $assessment;

    public function __construct(RiskAssessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function build()
    {
        return $this->subject('New Risk Assessment Submitted')
                    ->view('emails.risk_submitted');
    }
}
