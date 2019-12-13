<?php

namespace App\Services;

use App\Services\Factory\BroadcastMessagePathFactory;
use App\Services\Parser\XmlRequestMessageNotificationIdParser;
use App\Services\Validator\MessageNotificationIdValidator;

class MessageReceiver
{
    /** @var BroadcastMessagePathFactory */
    private $broadcastMessageFactory;

    /** @var XmlRequestMessageNotificationIdParser */
    private $xmlRequestMessageNotificationIdParser;

    /** @var MessageNotificationIdValidator */
    private $messageNotificationIdValidator;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        BroadcastMessagePathFactory $broadcastMessageFactory,
        XmlRequestMessageNotificationIdParser $xmlRequestMessageNotificationIdParser,
        MessageNotificationIdValidator $messageNotificationIdValidator
    ) {
        $this->broadcastMessageFactory = $broadcastMessageFactory;
        $this->xmlRequestMessageNotificationIdParser = $xmlRequestMessageNotificationIdParser;
        $this->messageNotificationIdValidator = $messageNotificationIdValidator;
    }

    public function receive(string $channelName, string $xmlRequest): string
    {
        $xmlRequest = trim($xmlRequest);
        $notificationId = $this->xmlRequestMessageNotificationIdParser->parse($xmlRequest);
        $this->messageNotificationIdValidator->validate($notificationId);

        $broadcastMessageFile = $this->broadcastMessageFactory->getMessageFilePath($channelName, $notificationId);
        @file_put_contents($broadcastMessageFile, $xmlRequest);

        return $notificationId;
    }
}