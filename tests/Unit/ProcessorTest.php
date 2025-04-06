<?php

namespace Tests\Unit;

use App\Processors\XlsProcessor;
use PHPUnit\Framework\TestCase;
use App\Processors\Storages\Redis as RedisStorage;

class ProcessorTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_xlsprocessor_can_read_valid_file(): void
    {
        $file = (dirname(__FILE__)).'/files/testfile.xlsx';
        $canReadTheFile = true;
        try{
            $processor = new XlsProcessor($file);
        } catch (\Throwable $e) {
            $canReadTheFile = false;
        }
        $processor = new XlsProcessor($file);
        $this->assertTrue($canReadTheFile);
    }

    public function test_xlsprocessor_can_accept_valid_storage(): void
    {
        $file = (dirname(__FILE__)).'/files/testfile.xlsx';
        $storage = new RedisStorage();
        $canAccept = true;
        try {
            $processor = new XlsProcessor($file, true, $storage);
        } catch (\Throwable $e) {
            $canAccept = false;
        }
        $this->assertTrue($canAccept);
    }

    public function text_xlsprocessor_can_validate_valid_file(): void
    {
        $file = (dirname(__FILE__)).'/files/testfile.xlsx';
        $processor = new XlsProcessor($file);
        $this->assertTrue($processor->fileIsValid());
    }

    public function text_xls_processor_can_set_value_in_redis_storage(): void
    {
        XlsProcessor::beginParsingProcess(10);
        $this->assertTrue(XlsProcessor::getLastParsedRow() == 10);
        XlsProcessor::endParsingProcess();
        $this->assertNull(XlsProcessor::getLastParsedRow());
    }
}
