<?php

namespace App\Contracts;
use Carbon\CarbonImmutable;

interface FailedJobsReaderInterface
{
    public function getData(CarbonImmutable $threshold);
}