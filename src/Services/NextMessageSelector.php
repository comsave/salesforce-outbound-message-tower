<?php

namespace App\Services;

use App\Services\Factory\BroadcastMessagePathFactory;

class NextMessageSelector
{
    /** @var BroadcastMessagePathFactory */
    private $broadcastMessageFactory;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(BroadcastMessagePathFactory $broadcastMessageFactory)
    {
        $this->broadcastMessageFactory = $broadcastMessageFactory;
    }

    public function nextMessage(string $channelName): ?string
    {
        $nextMessageFile = $this->nextMessageFile($channelName);

        return $nextMessageFile ? file_get_contents($nextMessageFile) : null;
    }

    public function nextMessageFile(string $channelName): ?string
    {
        $messageFiles = glob($this->broadcastMessageFactory->getMessageFilePath($channelName, '*'));

        usort($messageFiles, function (string $messageFileA, string $messageFileB) {
            return filemtime($messageFileA) > filemtime($messageFileB);
        });

        return $messageFiles[0] ?? null;
    }
}