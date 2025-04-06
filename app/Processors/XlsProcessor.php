<?php

namespace App\Processors;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use App\Processors\Storages\StorageInterface;
use App\Processors\Storages\Redis as RedisStorage;
use App\Events\ParsingProcess;

class XlsProcessor
{
    private static ?StorageInterface $storage = null;
    private string $file;

    public bool $skipHeader = true;

    public Worksheet $worksheet;

    private const PARSING_IN_PROCESS_KEY = 'parsing_in_process';

    const LAST_PARSED_KEY = 'last_parsed_key';

    const CHUNK_SIZE = 1000;


    public function __construct(string $file, $skipHeader = true, StorageInterface|null $storage = null)
    {
        $this->file = $file;
        $this->skipHeader = $skipHeader;
        if(!$this->fileIsValid()){
            throw new ReaderException('File is not valid.');
        }
        $spreadsheet = IOFactory::load($file, IReader::IGNORE_ROWS_WITH_NO_CELLS);
        $this->worksheet = $spreadsheet->getActiveSheet();


        // setting up Redis storage or whatever is needed
        self::setStorage($storage);
    }

    private static function setStorage(StorageInterface|null $storage): void
    {
        if($storage instanceof StorageInterface){
            self::$storage = $storage;
        } else {
            self::$storage = (new RedisStorage());
        }
    }

    private static function getStorage(): StorageInterface
    {
        if(!(self::$storage instanceof StorageInterface)){
            self::setStorage(null);
        }
        return self::$storage;
    }

    public function fileIsValid(): bool
    {
        try {
            $spreadsheet = IOFactory::load($this->file)->getActiveSheet()->getCell('A1');

        } catch(ReaderException) {
            return false;
        }
        return true;
    }

    public function countRows(): int
    {
        if($this->worksheet->getHighestRow() == 0){
            return 0;
        }

        if($this->skipHeader){
            return $this->worksheet->getHighestRow() - 1;
        }

        return $this->worksheet->getHighestRow();
    }

    public static function isParsingInProgress(): bool
    {
        return (bool) self::getStorage()::get(self::PARSING_IN_PROCESS_KEY);
    }

    public static function beginParsingProcess(): void
    {
        self::getStorage()::set(self::PARSING_IN_PROCESS_KEY, 1);
    }

    public static function endParsingProcess(): void
    {
        self::getStorage()::del(self::PARSING_IN_PROCESS_KEY);
    }

    public static function getLastParsedRow():int
    {
        return (int) self::getStorage()::get(self::LAST_PARSED_KEY);
    }

    public static function setLastParsedRow(int $row): void
    {
        self::getStorage()::set(self::LAST_PARSED_KEY, $row);
        ParsingProcess::dispatch($row);
    }

    public function createChunks(): array
    {
        $count =$this->countRows();
        $chunks = [];
        if($this->fileIsValid() && $count){
            $begin = $this->skipHeader ? 2 : 1;
            for($i=$begin;$i<=$count;$i+=self::CHUNK_SIZE){
                $chunk = [];
                $rowIterator = $this->worksheet->getRowIterator($i, $i+self::CHUNK_SIZE-1);
                foreach($rowIterator as $row){
                    $chunk[$row->getRowIndex()] = [];
                    $columnIterator = $row->getCellIterator('A', 'C');
                    foreach($columnIterator as $cell) {
                        $chunk[$row->getRowIndex()][$cell->getColumn()] = $cell->getValue();
                    }
                }
                $chunks[] = $chunk;
            }
        }
        return $chunks;
    }
}
