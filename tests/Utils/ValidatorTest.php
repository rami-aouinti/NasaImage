<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\Utils\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    private string $lastDay;

    protected function setUp(): void
    {
        $service = $this->getMockBuilder(HttpClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->validator = new Validator($service);
        $availableDates = $this->availableDate();
        if ($availableDates) {
            $this->lastDay = end($availableDates);
        } else {
            $this->lastDay = '2015-18-12';
        }
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
        $test = '2023-05-12';

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

    /**
     * @return string[]
     */
    private function availableDate(): array
    {
        return [
            '2023-11-10',
            '2023-11-11',
            '2023-11-12',
            '2023-11-13',
        ];
    }
}
