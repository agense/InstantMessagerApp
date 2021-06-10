<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\CarbonImmutable;
class FailedJobsReport extends Mailable
{
    use SerializesModels;

    private $reportDate;
    private $attachment;

    /**
     * Create a new message instance.
     * @return void
     */
    public function __construct(String $attachment, CarbonImmutable $reportDate)
    {
        $this->reportDate = $reportDate->format('Y-m-d h:s');
        $this->attachment = $attachment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(!file_exists($this->attachment)){
            throw new \Exception('Report attachment does not exist');
        }
        return $this->markdown('reports.emails.failed_jobs', ['reportDate' => $this->reportDate])
        ->attach($this->attachment, [
            'mime' => 'application/pdf',
        ]);
    }
}
