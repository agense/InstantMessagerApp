<?php

return [
    'thresholds' => [
        'failed_jobs_retry_within_minutes' => env('FAILED_JOBS_RETRY_WITHIN_MINUTES', 120),
        'failed_jobs_log_every_hours' => env('FAILED_JOBS_LOG_EVERY_HOURS', 24),
        'failed_jobs_clean_every_hours' => env('FAILED_JOBS_CLEAN_EVERY_HOURS', 48),
    ],
    'reporting' => [
        'failed_jobs_reporting_interval_hours' => env('ISSUE_FAILED_JOBS_REPORTS_FOR_HOURS', 24),
        'failed_jobs_reporting_data_src' => env('FAILED_JOBS_REPORTING_DATA_SRC', 'log'),
        'failed_jobs_report_email' => env('FAILED_JOBS_REPORT_EMAIL', null),
    ],
];