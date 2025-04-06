<?php

namespace App\Processors\Validators;
use Carbon\Carbon;
use DateTime;
use Throwable;

class RowValidator
{

    public private(set) array $errors = [];

    public static string $format = 'd.m.Y';

    /**
     * @param string|int $a
     * @param string $b
     * @param string $c
     */
    public function __construct(public string|int|null $a, public string|null $b, public string|null $c)
    {

    }

    /**
     * @return bool
     */
    public function isValidA(): bool
    {
        $valid = ($this->a == (int) $this->a) && ((int) $this->a > 0);
        if(is_null($this->a)){
            $this->errors['A'] = ' empty ID ';
            return false;
        }

        if(!$valid){
            $this->errors['A'] = ' invalid ID ';
        }

        return $valid;
    }

    /**
     * @return bool
     */
    public function isValidB(): bool
    {
        $valid = (bool) preg_match('/^[a-zA-Z\s]*$/', $this->b);

        if(!$valid){
            $this->errors['B'] = ' invalid name ';
        }
        return $valid;
    }

    /**
     * @return bool
     */
    public function isValidC(): bool
    {
        $testDate = $this->c;
        if(!trim($testDate)){
            $this->errors['C'] = ' empty date ';
            return false;
        }
        $error = ' wrong date format ';

        $date = DateTime::createFromFormat(self::$format, $testDate);
        $valid = $date && strtolower($date->format(self::$format)) === strtolower($this->c);
        if(!$valid){
            $this->errors['C'] = $error;
            return false;
        }

        try {
            Carbon::createFromFormat(RowValidator::$format, $testDate)->format(self::$format);
        } catch(Throwable $e) {
            $this->errors['C'] = $error;
            return false;
        }

        if(!preg_match('/^(0?[1-9]|[12][0-9]|3[01])\.(0?[1-9]|1[0-2])\.(\d{4})$/', $testDate)){
            $this->errors['C'] = $error;
            $valid = false;
        }

        if(!$valid){
            $this->errors['C'] = $error;
        }
        return $valid;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $isValidA = $this->isValidA();
        $isValidB = $this->isValidB();
        $isValidC = $this->isValidC();
        return ($isValidA && $isValidB && $isValidC);
    }

    /**
     * @param array $haystack
     * @return bool
     */
    public function inHaystack(array $haystack = []): bool
    {
        $inHaystack = in_array($this->a, $haystack);
        if($inHaystack){
            $this->errors['duplicate'] =  ' duplicate ID ';
        }
        return $inHaystack;
    }
}
