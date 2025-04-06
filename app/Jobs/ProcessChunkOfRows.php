<?php

namespace App\Jobs;

use App\Processors\XlsProcessor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Processors\Validators\RowValidator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Batchable;
use App\Models\Row;
use Illuminate\Support\Facades\Log;

class ProcessChunkOfRows implements ShouldQueue
{
    use Batchable, Queueable;

    private array $chunk;

    /**
     * Create a new job instance.
     */
    public function __construct(array $chunk)
    {
        $this->chunk = $chunk;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $errors = [];
        $rowsToInsert = [];
        $uniqueIDs = [];
        $lastRow = 0;
        foreach($this->chunk as $index => $arrayRow){
            $validator = new RowValidator($arrayRow['A'], $arrayRow['B'], $arrayRow['C']);
            $isValid = $validator->isValid();
            if(!$isValid){
                $errors[$index] = $validator->errors;
            }

            if($validator->inHaystack($uniqueIDs)){
                $errors[$index] = $validator->errors;
            }

            if(!isset($errors[$index])){
                $uniqueIDs[] = $arrayRow['A'];
                $rowsToInsert[$index] = [
                    'row_id' => $arrayRow['A'],
                    'name' => $arrayRow['B'],
                    'date' => Carbon::createFromFormat(RowValidator::$format, $arrayRow['C']),
                    'created_at' => Carbon::now(),
                ];
            } else {
                //writing to logfile
                $info = "Row $index - ".implode(', ', $errors[$index])."\n";
                Log::channel('txtfile')->info($info);
            }
            $lastRow = $index;
        }

        $uniqueIDs = array_values(array_unique($uniqueIDs));
        // 1-dimensional array of duplicates in DB
        $duplicates = Row::whereIn('row_id', $uniqueIDs)->get(['row_id'])->pluck('row_id')->toArray();

        $data = array_values(array_filter($rowsToInsert, function($v, $k) use ($duplicates) {
            if(in_array((int)$v['row_id'], $duplicates)){
                Log::channel('txtfile')->info("Row ".$k." - duplicate \n");
            }
            return !in_array((int)$v['row_id'], $duplicates);
        }, ARRAY_FILTER_USE_BOTH));

        // Model observer methods don't work here
        DB::table('rows')->insertOrIgnore($data);
        XlsProcessor::setLastParsedRow((int) $lastRow);
    }
}
