<?php
namespace App\Services;

use Carbon\CarbonImmutable;
use Barryvdh\DomPDF\Facade as PDF;

class FailedJobsReportingService{

    private $storagePath;
    private $now;
    private $threshold;
    private $data;
    private $filename;

    public function __construct(Array $data, CarbonImmutable $now, CarbonImmutable $threshold){
        $this->now = $now;
        $this->threshold = $threshold;
        $this->data = $data;
        $this->storagePath = storage_path('reports');
    }

    public function __invoke(){
        $this->createPDFReport();
        return $this->filename;
    }

    private function createPDFReport(){
        $data = array();
        $data['jobs'] = $this->data;
        $data['total'] = count($this->data);
        $data['from'] = $this->threshold->format('Y-m-d h:s');
        $data['to'] = $this->now->format('Y-m-d h:s');
    
        //Make PDF report
        $date = $this->now->format('Y_m_d');
        $this->filename = $this->storagePath."/failed_jobs_report_".$date.".pdf";
        $pdf = PDF::loadView('reports.failed_jobs_template', compact('data'))
        ->setOptions(['defaultFont' => 'sans-serif'])
        ->setPaper('a4', 'landscape');
        return $pdf->save($this->filename);
    }
}