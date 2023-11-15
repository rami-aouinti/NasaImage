<?php

declare(strict_types=1);

/**
 * (c) Rami Aouinti <rami.aouinti@gmail.com>
 **/

namespace App\Command;

use App\Helper\AvailableDatesHelper;
use App\Helper\AvailableImagesHelper;
use App\Service\ImageUploader;
use App\Utils\Validator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Stopwatch\Stopwatch;

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

    private SymfonyStyle $io;

    final public const NAME = 'nasa:images';
    private const PARAMETER_NAME = 'name';
    private const PARAMETER_DESCRIPTION = 'description';

    /**
     * @param AvailableDatesHelper $availableDatesHelper
     * @param AvailableImagesHelper $availableImagesHelper
     * @param ImageUploader $imageUploader
     * @param Validator $validator
     */
    public function __construct(
        private AvailableDatesHelper $availableDatesHelper,
        private AvailableImagesHelper $availableImagesHelper,
        private ImageUploader $imageUploader,
        private Validator $validator
    )
    {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null !== $input->getArgument('folder')) {
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

        // Ask for the username if it's not defined
        $folder = $input->getArgument('folder');
        if (null !== $folder) {
            $this->io->text(' > <info>Folder</info>: '.$folder);
        } else {
            $folder = $this->io->ask('Folder Name', null, $this->validator->validateFolder(...));
            $input->setArgument('folder', $folder);
        }

        // Ask for the password if it's not defined
        /** @var string|null $date */
        $date = $input->getArgument('date');

        if (null !== $date) {
            $this->io->text(' > <info>Date</info>: '.$date);
        } else {
            $date = $this->io->ask(
                'Date (Example : 2015-18-12)',
                null,
                $this->validator->validateDate(...));
            $input->setArgument('date', $date);
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
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
            else

                $images = $this->availableImagesHelper->getData($this->validateAvailableDates($date));
                foreach ($images as $image) {
                    $this->imageUploader->uploadFromUrlToFolder($image['image'], $folderName, $date);
                }
                $this->io->success(
                    sprintf('Images was successfully imported and saved under : %s', $folderName )
                );
                $event = $stopwatch->stop('import-nasa-images');
                if ($output->isVerbose()) {
                    $this->io->comment(
                        sprintf(
                            'New folder : %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB',
                            $folderName, $event->getDuration(),
                            $event->getMemory() / (1024 ** 2)
                        )
                    );
                }
                return Command::SUCCESS;
    }

    /**
     * @param string|null $date
     * @return string|null
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function validateAvailableDates(?string $date): ?string
    {
        $available_dates = $this->availableDatesHelper->getData();
        if (!empty($date))
        {
            if (in_array($date, $available_dates)) {
                return $date;
            } else {
                return null;
            }
        } else {
            return end($available_dates);
        }
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

              <info>php %command.folder%</info> <comment>username password email</comment>

              <info>php %command.full_name%</info> username password email <comment>--admin</comment>

            If you omit any of the three required arguments, the command will ask you to
            provide the missing values:

              # command will ask you for the email
              <info>php %command.full_name%</info> <comment>username password</comment>

              # command will ask you for the email and password
              <info>php %command.full_name%</info> <comment>username</comment>

              # command will ask you for all arguments
              <info>php %command.full_name%</info>
            HELP;
    }
}
