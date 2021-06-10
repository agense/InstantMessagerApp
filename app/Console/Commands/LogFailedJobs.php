<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FailedJobsDBReaderService;
use App\Services\FailedJobsLoggerService;
use Carbon\CarbonImmutable;


class LogFailedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'failedjobs:log 
    {--hours= : Interval in hours specifying the timeframe for which to select jobs for logging counting from job failure time till current moment  }
    {--within : If passed, jobs that failed withing the specified hours interval from calling moment will be logged, otherwise jobs that failed before that time will be logged }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logs failed jobs from table to a log file based on their failure time and specified interval';

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
     * Execute the console command.
     * @return void
     */
    public function handle(FailedJobsDBReaderService $source)
    {
        $interval = $this->option('hours') ?? config('failed_jobs.thresholds.failed_jobs_log_every_hours');
        $now = CarbonImmutable::now();
        $threshold = $now->subHours($interval); 
        $logCount = 0;

        try{
           $failedJobs = $source->getData($threshold, $this->option('within'));

            if(!empty($failedJobs)){
                $loggedJobs = (new FailedJobsLoggerService($failedJobs, $now))();
                $logCount = count($loggedJobs);
            }
            $message = $logCount > 0 ? "$logCount failed jobs logged" : "No new jobs were found to be logged";
            $this->info($message);

        }catch(\Exception $e){
            $this->error($e->getMessage());
        } 
    }
}
