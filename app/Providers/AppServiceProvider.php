<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\FailedJobsDBReaderService;
use App\Services\FailedJobsLogReaderService;
use App\Contracts\FailedJobsReaderInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FailedJobsReaderInterface::class, function ($app) {
            if(config('failed_jobs.reporting.failed_jobs_reporting_data_src') == 'database'){
                return new FailedJobsDBReaderService();
            }else{
                return new FailedJobsLogReaderService();
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
    }
}
