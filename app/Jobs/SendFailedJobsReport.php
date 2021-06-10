<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\FailedJobsReport;
use Carbon\CarbonImmutable;

class SendFailedJobsReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $reportDate;
    private $attachment;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct(String $attachment, CarbonImmutable $reportDate)
    {
        $this->reportDate = $reportDate;
        $this->attachment = $attachment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recipient = config('failed_jobs.reporting.failed_jobs_report_email');
        if(!$recipient){
            throw new \Exception('Reporting Email is not set');
        }
        //Send the report email
        Mail::to($recipient)->send(new FailedJobsReport($this->attachment,  $this->reportDate));

        //Delete attatchment after sending email
        unlink($this->attachment);
    }
}
