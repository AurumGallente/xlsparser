<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Processors\Validators\RowValidator;

class ValidatorTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_validator_checks_empty_id(): void
    {
        $id = '';
        $name = 'John Snow';
        $date = '1.12.1970';
        $validator = new RowValidator($id, $name, $date);
        $this->assertFalse($validator->isValidA());
    }

    public function test_validator_checks_valid_id(): void
    {
        $id = '123456';
        $name = 'John Snow';
        $date = '1.12.1970';
        $validator = new RowValidator($id, $name, $date);
        $this->assertTrue($validator->isValidA());
    }

    public function test_validator_checks_invalid_name(): void
    {
        $id = '123';
        $name = 'md516b7/&';
        $date = '1.12.1970';
        $validator = new RowValidator($id, $name, $date);
        $this->assertFalse($validator->isValidB());
    }

    public function test_validator_checks_valid_name(): void
    {
        $id = '123';
        $name = 'John Snow';
        $date = '1.12.1970';
        $validator = new RowValidator($id, $name, $date);
        $this->assertTrue($validator->isValidB());
    }

    public function test_validator_checks_invalid_date_format(): void
    {
        $id = '123';
        $name = 'John Snow';
        $date = '1/12/1970';
        $validator = new RowValidator($id, $name, $date);
        $this->assertFalse($validator->isValidC());

        $date = '1.2.1985';
        $validator = new RowValidator($id, $name, $date);
        $this->assertFalse($validator->isValidC());

        $date = '1.Apr.1985';
        $validator = new RowValidator($id, $name, $date);
        $this->assertFalse($validator->isValidC());
    }

    public function test_validator_checks_empty_date(): void
    {
        $id = '123';
        $name = 'John Snow';
        $date = '';
        $validator = new RowValidator($id, $name, $date);
        $this->assertFalse($validator->isValidC());
    }

    public function test_validator_checks_american_date_format(): void
    {
        $id = '123';
        $name = 'John Snow';
        $date = '10.31.1968';
        $validator = new RowValidator($id, $name, $date);
        $this->assertFalse($validator->isValidC());
    }

    public function test_validator_checks_valid_date(): void
    {
        $id = '123';
        $name = 'John Snow';
        $date = '11.02.1968';
        $validator = new RowValidator($id, $name, $date);
        $this->assertTrue($validator->isValidC());
    }

    public function test_validator_checks_duplicates(): void
    {
        $array = [123, 321, 567];
        $id = '123';
        $name = 'John Snow';
        $date = '11.02.1968';
        $validator = new RowValidator($id, $name, $date);
        $this->assertTrue($validator->inHaystack($array));

    }

    public function test_validator_checks_valid_row(): void
    {
        $id = 1234567890;
        $name = 'Jane Frame';
        $date = '11.03.1991';
        $validator = new RowValidator($id, $name, $date);
        $this->assertTrue($validator->isValidC());
    }
}
