<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FailedJobsDBReaderService;
use Carbon\CarbonImmutable;

class CleanFailedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'failedjobs:clean {--hours= : Number of hours that must have past from job failure time for it to be deleted }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes failed jobs from table and logs them to a log file based on their failure time and specified interval';

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
        $interval = $this->option('hours') ?? config('failed_jobs.thresholds.failed_jobs_clean_every_hours');
        $now = CarbonImmutable::now();
        $threshold = $now->subHours($interval); 

        try{
            $failedJobs = $source->getData($threshold, false);
            if(!empty($failedJobs)){
                //Log jobs being deleted to filed_jobs log file
                $this->call('failedjobs:log', ['--hours' => $interval]);

                //Delete jobs from db
                $failedIds = collect($failedJobs)->pluck('uuid')->toArray();
                
                foreach($failedIds as $jobId){
                    $this->call('queue:forget', ['id' => $jobId]);
                }
            }  
            $deleteCount = count($failedJobs);
            $deletionMessage = ($deleteCount%2 == 0) ? "$deleteCount failed jobs were deleted from database" : "$deleteCount failed job was deleted from database"; 
            $this->info($deletionMessage);  
        }catch(\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
