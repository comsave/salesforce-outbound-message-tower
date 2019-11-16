<?php

namespace App\Services;

use App\Exception\OutboundMessageTowerException;
use App\Validator\MessageNotificationIdValidator;

class ProcessedMessageRemover
{
    /** @var MessageNotificationIdValidator */
    private $messageNotificationIdValidator;

    /** @var string */
    private $broadcastMessagesDir;

    public function __construct(
        MessageNotificationIdValidator $messageNotificationIdValidator,
        string $broadcastMessagesDir
    ) {
        $this->messageNotificationIdValidator = $messageNotificationIdValidator;
        $this->broadcastMessagesDir = $broadcastMessagesDir;
    }

    /**
     * @param string|null $notificationId
     * @throws OutboundMessageTowerException
     */
    public function remove(?string $notificationId): void
    {
        $this->messageNotificationIdValidator->validate($notificationId);

        $broadcastMessageFile = sprintf('%s/%s.xml', $this->broadcastMessagesDir, $notificationId);

        @unlink($broadcastMessageFile);
    }
}