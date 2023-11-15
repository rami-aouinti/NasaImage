<?php

/**
 * (c) Rami Aouinti <rami.aouinti@gmail.com>
 **/

namespace App\Tests\Utils;

use App\Helper\AvailableDatesHelper;
use App\Utils\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    private Validator $validator;

    private string $lastDay;


    protected function setUp(): void
    {
        $service = $this->getMockBuilder(AvailableDatesHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->validator = new Validator($service);
        $availableDates = $service->getData();
        print_r($availableDates);
        $this->lastDay = end($availableDates);
    }

    public function testValidateFolder(): void
    {
        $test = 'folder';

        $this->assertSame($test, $this->validator->validateFolder($test));
    }

    public function testValidateFolderEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The folder name can not be empty.');
        $this->validator->validateFolder(null);
    }

    public function testValidateFolderInvalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The folder name must contain only lowercase latin characters and underscores.');
        $this->validator->validateFolder('INVALID');
    }

    public function testValidateDate(): void
    {
        $test = 'date';

        $this->assertSame($test, $this->validator->validateDate($test));
    }

    public function testValidateDateEmpty(): void
    {
        $this->assertSame($this->lastDay, $this->validator->validateDate(null));
    }

    public function testValidateDateInvalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The format of invalid data is invalid.');
        $this->validator->validateDate('invalid data');
    }

}
