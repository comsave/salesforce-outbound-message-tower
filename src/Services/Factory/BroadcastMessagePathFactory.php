<?php

namespace App\Services\Factory;

class BroadcastMessagePathFactory
{
    /** @var string */
    private $broadcastMessagesDir;

    public function __construct(string $broadcastMessagesDir)
    {
        $this->broadcastMessagesDir = $broadcastMessagesDir;
    }

    public function getMessageDirectory(string $channelName): string
    {
        $messageDir = sprintf('%s/%s', $this->broadcastMessagesDir, $channelName);

        if(!file_exists($messageDir)) {
            mkdir(dirname($messageDir));
        }

        return $messageDir;
    }

    public function getMessageFilePath(string $channelName, string $notificationId): string
    {
        return sprintf('%s/%s.xml', $this->getMessageDirectory($channelName), $notificationId);
    }
}