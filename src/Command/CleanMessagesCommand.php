<?php

namespace App\Command;

use App\Services\NextMessageSelector;
use App\Services\ProcessedMessageRemover;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanMessagesCommand extends Command
{
    protected static $defaultName = 'app:clean:messages';

    /** @var NextMessageSelector */
    private $nextMessageSelector;

    /** @var ProcessedMessageRemover */
    private $processedMessageRemover;

    public function __construct(NextMessageSelector $nextMessageSelector, ProcessedMessageRemover $processedMessageRemover)
    {
        parent::__construct();

        $this->nextMessageSelector = $nextMessageSelector;
        $this->processedMessageRemover = $processedMessageRemover;
    }

    protected function configure()
    {
        $this
            ->setDescription('Removes old unprocessed outbound messages.')
            ->addArgument('maxDays', InputArgument::OPTIONAL, 'Maximum age of unprocessed messages in days.', 30);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $maxDays = $input->getArgument('maxDays');

        $removedMessageCount = $this->removeOldUnprocessedMessages($maxDays);

        $io->success(sprintf('%s messages removed.', $removedMessageCount));

        return 0;
    }

    public function removeOldUnprocessedMessages(int $maxDaysOld): int
    {
        $beforeTimestamp = (new \DateTime('now'))->modify(sprintf('-%s day', $maxDaysOld))->getTimestamp();
        $allMessageFilePaths = $this->nextMessageSelector->getAllMessageFiles('*');
        $removedMessageCount = 0;

        foreach ($allMessageFilePaths as $messageFilePath) {
            if (filemtime($messageFilePath) < $beforeTimestamp) {
                $messageFilePathData = explode('/', $messageFilePath);

                $notificationId = array_pop($messageFilePathData);
                $notificationId = explode('.', basename($notificationId))[0];

                $channelName = array_pop($messageFilePathData);

                $this->processedMessageRemover->remove($channelName, $notificationId);

                $removedMessageCount++;
            }
        }

        return $removedMessageCount;
    }
}
