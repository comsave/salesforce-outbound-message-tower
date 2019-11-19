<?php

namespace App\Services;

class NextMessageSelector
{
    /** @var string */
    private $broadcastMessagesDir;

    public function __construct(string $broadcastMessagesDir)
    {
        $this->broadcastMessagesDir = $broadcastMessagesDir;
    }

    public function nextMessage(): ?string
    {
        $nextMessageFile = $this->nextMessageFile();

        return $nextMessageFile ? file_get_contents($nextMessageFile) : null;
    }

    public function nextMessageFile(): ?string
    {
        $messageFiles = glob(sprintf('%s/*.xml', $this->broadcastMessagesDir));

        usort($messageFiles, function (string $messageFileA, string $messageFileB) {
            return filemtime($messageFileA) > filemtime($messageFileB);
        });

        return $messageFiles[0] ?? null;
    }
}