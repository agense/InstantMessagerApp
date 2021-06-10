<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FailedJobsDBReaderService;
use Carbon\CarbonImmutable;

class RetryFailedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'failedjobs:retry {--minutes= : Only jobs that failed not earlier than this number of minutes from the current moment will be retried }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pushes failed jobs back to the queue based on their failure time and specified interval';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Push all jobs that are newer than the specified threshold date back to jobs queue
     * @return Void
     */
    public function handle(FailedJobsDBReaderService $source)
    {
        $retryTime = $this->option('minutes') ?? config('failed_jobs.thresholds.failed_jobs_retry_within_minutes');
        $threshold = CarbonImmutable::now()->subMinutes($retryTime); 

        $failedJobs = $source->getData($threshold);
        
        if(!empty($failedJobs)){
            $failedIds = collect($failedJobs)->pluck('uuid')->toArray();
            $this->call('queue:retry', ['id' => $failedIds]);
        }else{
            $this->info("There are no failed jobs for period specified");
        }
    }
}
