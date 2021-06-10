<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FailedJobsReportingService;
use App\Contracts\FailedJobsReaderInterface;
use App\Jobs\SendFailedJobsReport;
use Carbon\CarbonImmutable;

class CreateFailedJobsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'failedjobs:report {--hours= : Interval in hours within which from the call time the job must have failed to be included in the report }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a failed jobs report for specific time interval and sends it by mail as PDF';

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
    public function handle(FailedJobsReaderInterface $source)
    {
        $interval = $this->option('hours') ?? config('failed_jobs.reporting.failed_jobs_reporting_interval_hours');
        $now = CarbonImmutable::now();
        $threshold = $now->subHours($interval);
        
        try{
            //If data source is log, log the jobs for specific period from database first in case there are any missing
            if(config('failed_jobs.reporting.failed_jobs_reporting_data_src') !== 'database'){
                $this->call('failedjobs:log', ['--hours' => $interval, '--within' => true]);
            }
            $data = $source->getData($threshold);

            //Create a PDF Report
            $report = (new FailedJobsReportingService($data, $now, $threshold))();
            
            //Send Reporting Email Via a Job Class
            SendFailedJobsReport::dispatch($report, $now);

            $this->info('Report Created');
        }catch(\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
