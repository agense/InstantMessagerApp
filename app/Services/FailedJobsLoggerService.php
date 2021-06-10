<?php
namespace App\Services;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class FailedJobsLoggerService{

    private $logChannel = 'custom';
    private $jobs;
    private $time;
    private $logPath = 'storage/logs/failed_jobs.log';

    public function __construct(Array $jobs, CarbonImmutable $time){
        $this->jobs = $jobs;
        $this->time = $time;
    }

    public function __invoke(){

        //Remove jobs that have already been logged
        $lastId = $this->getLastLoggedId();
        $newJobs = array_filter($this->jobs, function($job) use($lastId){
            return $job->id > $lastId;
        });
        //Log failed jobs
        foreach($newJobs as $job){
            Log::channel($this->logChannel)->info($this->createLogMessage($job));
        }
        return $newJobs;
    }

    private function getLastLoggedId(){
        $fp = fopen($this->logPath, 'r');
        //Get Last Log Line
        $pos = -2;
        $line = "";
        while(-1 !== fseek($fp, $pos, SEEK_END)){
            $pos--;
            fseek($fp, $pos, SEEK_END);
            $char = fgetc($fp);
            if ($char == "\n" || $char == "\r" || ftell($fp) <= 1) {
                $line = fgets($fp);
                break;
            }
        }
        fclose($fp);
    
        //Get the numeric Id of the last logged job 
        $lastId=1;
        if(strlen(trim($line)) > 0){
            $lastId = intval(trim(Str::between($line, 'Id:', 'Event:')));
        }
        return $lastId;
    }

    private function createLogMessage($job){
        $logMessage = '';
        $payload = json_decode($job->payload);
        $exception = trim(Str::before($job->exception, 'Stack trace:'));
        $logMessage .= "Id: $job->id ";
        $logMessage .= "Event: $payload->displayName (uuid: $job->uuid) ";
        $logMessage .= "Failed at: $job->failed_at ";
        $logMessage .= "Queue: $job->queue ";
        $logMessage .= "Connection: $job->connection ";
        $logMessage .= "Exception: $exception ";
        $logMessage .= "Logged at: $this->time ";
        return $logMessage;
    }
}