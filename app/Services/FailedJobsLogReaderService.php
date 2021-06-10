<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Contracts\FailedJobsReaderInterface;

class FailedJobsLogReaderService implements FailedJobsReaderInterface {

    private $path = 'storage/logs/failed_jobs.log';
    private $data = array();
    private $threshold;

    public function getData(CarbonImmutable $threshold){
        $fp = fopen($this->path, 'r');
        self::readData($fp, $threshold);
        fclose($fp);
        return $this->data;
    }

    private function readData($fp, $threshold){
        // Until fseek returns -1, seek the end of file with offset specified by $pos variable
        // Decrease $pos by 1 every time in a loop so that the offset from the end of file increases by one character each time in the loop
        $pos = -2;
        while (-1 !== fseek($fp, $pos, SEEK_END)) {
            //Get the character at current position in the loop. 
            //If the characted indicates the end of line, get the current line
            //If the current cursor position is at 1 or 0, i.e. at the beginning of the file get the current line
            $char = fgetc($fp);
            if ($char == "\n" || $char == "\r" || ftell($fp) <= 1) {
                    $line =  fgets($fp); 
                    // Create a Carbon Instance from jobs Deleted at string
                    $timestamp = Carbon::create(trim(Str::between($line, ' Failed at: ', ' Queue: ')));

                    //If jobs deletion time is before the specified threshold date, stop reading the log
                    if($timestamp->lessThanOrEqualTo($threshold)){
                         break;
                    }else{
                        if(strlen(trim($line)) > 0){
                            self::formatDataArray($line);
                        }
                    }
            } 
            $pos--;
        }
    }

    private function formatDataArray($line){
        $obj = new \stdClass();
        $obj->name = trim(Str::between($line, ' Event:', ' (uuid:'));
        $obj->uuid = trim(Str::between($line, ' (uuid:', ' Failed at:'));
        $obj->failed_at = trim(Str::between($line, ' Failed at: ', ' Queue: '));
        $obj->queue = trim(Str::between($line, ' Queue: ', ' Connection: '));
        $obj->connection = trim(Str::between($line, ' Connection: ', ' Exception: '));
        $obj->exception = trim(Str::between($line, ' Exception: ', ' Logged at:'));
        array_push($this->data, $obj);
    }
}