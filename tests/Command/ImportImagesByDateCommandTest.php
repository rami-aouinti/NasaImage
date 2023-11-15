<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ImportImagesByDateCommand;

class ImportImagesByDateCommandTest extends AbstractCommandTest
{
    /**
     * @var string[]
     */
    private array $inputs = [
        'folder' => 'test_folder',
        'date' => '2015-11-12',
    ];

    /**
     * @var string[]
     */
    private array $emptyInputs = [
        'folder' => '',
        'date' => '',
    ];

    protected function setUp(): void
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            $this->markTestSkipped('`stty` is required to test this command.');
        }
    }

    /**
     * This test provides all the arguments required by the command, so the
     * command runs non-interactively and it won't ask for any argument.
     */
    public function testSaveImagesNonInteractive(): void
    {
        $input = $this->inputs;
        $this->executeCommand($input);
        $this->assertImagesCreated();
    }

    public function testSaveImagesInteractive(): void
    {
        $this->executeCommand(
            $this->emptyInputs,
            array_values($this->inputs)
        );
        $this->assertImagesCreated();
    }

    protected function getCommandFqcn(): string
    {
        return ImportImagesByDateCommand::class;
    }

    private function assertImagesCreated(): void
    {
        $filename = './public/nasa-images/test_folder/2015/11/12/epic_1b_20151112010437.png';
        $this->assertTrue(file_exists($filename));
    }
}
