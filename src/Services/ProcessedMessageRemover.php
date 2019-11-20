<?php

namespace App\Services;

use App\Exception\OutboundMessageTowerException;
use App\Services\Factory\BroadcastMessagePathFactory;
use App\Services\Validator\MessageNotificationIdValidator;

class ProcessedMessageRemover
{
    /** @var BroadcastMessagePathFactory */
    private $broadcastMessageFactory;

    /** @var MessageNotificationIdValidator */
    private $messageNotificationIdValidator;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        BroadcastMessagePathFactory $broadcastMessageFactory,
        MessageNotificationIdValidator $messageNotificationIdValidator
    ) {
        $this->broadcastMessageFactory = $broadcastMessageFactory;
        $this->messageNotificationIdValidator = $messageNotificationIdValidator;
    }

    public function remove(string $channelName, string $notificationId): void
    {
        $this->messageNotificationIdValidator->validate($notificationId);

        $broadcastMessageFile = $this->broadcastMessageFactory->getMessageFilePath($channelName, $notificationId);

        @unlink($broadcastMessageFile);
    }
}