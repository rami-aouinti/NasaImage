<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ImageUploader;
use App\Utils\Validator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class ImportImagesByDateCommand
 *
 * @package App\NasaImage
 */
#[AsCommand(
    name: self::NAME,
    description: 'Console command to create folder of images from nasa api',
)]
class ImportImagesByDateCommand extends Command
{
    final public const NAME = 'nasa:images';
    final public const URL = 'https://epic.gsfc.nasa.gov/api/images.php?available_dates';
    private SymfonyStyle $io;

    public function __construct(
        private ImageUploader $imageUploader,
        private Validator $validator,
        private HttpClientInterface $client
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getCommandHelp())
            ->addArgument('folder', InputArgument::OPTIONAL, 'The name of the new folder')
            ->addArgument('date', InputArgument::OPTIONAL, 'The date');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {

        if ($input->getArgument('folder') !== null) {
            return;
        }

        $this->io->title('Import Images From Nasa Api Command');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console nasa:images folder date',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        $folder = $this->io->ask('Folder Name', null, $this->validator->validateFolder(...));
        $input->setArgument('folder', $folder);

        $date = $this->io->ask(
            'Date (Example : 2023-11-13)',
            null,
            $this->validator->validateDate(...)
        );
        $input->setArgument('date', $date);

        if ($output->isVerbose()) {
            $this->io->comment(
                sprintf(
                    'New folder : %d / Elapsed time',
                    $folder
                )
            );
        }
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('import-nasa-images');

        /** @var string $folderName */
        $folderName = $input->getArgument('folder');

        /** @var string $date */
        $date = $input->getArgument('date');

        if ($this->validateAvailableDates($date) === null)
            throw new RuntimeException(sprintf('In this date "%s" no image available', $date));

        $this->imageUploader->uploadFromUrlToFolder($folderName, $date);

        $this->io->success(
            sprintf('Images was successfully imported and saved under : %s', $folderName)
        );
        $event = $stopwatch->stop('import-nasa-images');
        if ($output->isVerbose()) {
            $this->io->comment(
                sprintf(
                    'New folder : %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB',
                    $folderName,
                    $event->getDuration(),
                    $event->getMemory() / (1024 ** 2)
                )
            );
        }

        return Command::SUCCESS;
    }

    private function validateAvailableDates(?string $date): ?string
    {
        $available_dates = $this->client->request('GET', self::URL)->toArray();
        if (count($available_dates) > 0) {
            if ($date !== null) {
                if (in_array($date, $available_dates, true)) {
                    return $date;
                }

                return null;
            }

            return end($available_dates);
        }

        return null;
    }

    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     */
    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info>
            command importes images from nasa api and saves them in the a specific folder:

              <info>php %command.folder%</info> <comment>folder</comment>

            If you omit any of the three required arguments, the command will ask you to
            provide the missing values:

              # command will ask you for the folder name
              <info>php %command.folder%</info> <comment>folder</comment>

              # command will ask you for the date
              <info>php %command.date%</info> <comment>date</comment>

              # command will ask you for all arguments
              <info>php %command.folder%</info>
            HELP;
    }
}
