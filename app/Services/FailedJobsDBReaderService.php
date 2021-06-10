<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Contracts\FailedJobsReaderInterface;

class FailedJobsDBReaderService implements FailedJobsReaderInterface {

    public function getData(CarbonImmutable $threshold, Bool $within = true){

        $operand = $within ? ">=" : "<=";
        $data = DB::select("select * from failed_jobs where failed_at $operand ?", [$threshold]);

        $data = array_map(function($job){
            $payload = json_decode($job->payload);
            $job->name = $payload->displayName;
            $job->failed_at = date('Y-m-d h:s', strtotime($job->failed_at));
            $job->exception = Str::before($job->exception, 'Stack trace:');
            return $job;
        }, $data);
        return $data;
    }
}