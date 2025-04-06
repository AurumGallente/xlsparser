<?php

namespace App\Jobs;

use App\Processors\XlsProcessor;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;


class InitChunks implements ShouldQueue
{
    use Queueable;

    private $file;

    /**
     * Create a new job instance.
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $processor = new XlsProcessor($this->file);
        $chunks = $processor->createChunks();

        $arrayOfJobs = [];
        DB::table('rows')->truncate();
        foreach($chunks as $chunk) {
            $arrayOfJobs[] = new ProcessChunkOfRows($chunk);
        }
        $batch = Bus::batch($arrayOfJobs)
            ->before(function (Batch $batch)  {
                if(XlsProcessor::getLastParsedRow()){
                    $batch->cancel();
                }
            })
            ->progress(function (Batch $batch) {
                // add laravel Echo here
            })
            ->catch(function (Batch $batch) {
                XlsProcessor::endParsingProcess();
                $batch->cancel();
            })
            ->finally(function (Batch $batch) {
                XlsProcessor::endParsingProcess();
            })->onQueue('chunks')->dispatch();
    }
}
