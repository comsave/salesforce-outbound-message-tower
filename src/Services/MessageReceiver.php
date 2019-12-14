<?php

namespace App\Services;

use App\Services\Builder\RedisClientBuilder;
use App\Services\Parser\XmlRequestMessageNotificationIdParser;
use App\Services\Validator\MessageNotificationIdValidator;

class MessageReceiver
{
    /** @var XmlRequestMessageNotificationIdParser */
    private $xmlRequestMessageNotificationIdParser;

    /** @var MessageNotificationIdValidator */
    private $messageNotificationIdValidator;

    /** @var RedisClientBuilder */
    private $redisClientBuilder;

    public function __construct(
        XmlRequestMessageNotificationIdParser $xmlRequestMessageNotificationIdParser,
        MessageNotificationIdValidator $messageNotificationIdValidator,
        RedisClientBuilder $redisClientBuilder
    ) {
        $this->xmlRequestMessageNotificationIdParser = $xmlRequestMessageNotificationIdParser;
        $this->messageNotificationIdValidator = $messageNotificationIdValidator;
        $this->redisClientBuilder = $redisClientBuilder;
    }

    public function receive(string $channelName, string $xmlRequest): string
    {
        $notificationId = $this->xmlRequestMessageNotificationIdParser->parse($xmlRequest);
        $this->messageNotificationIdValidator->validate($notificationId);

        $this->redisClientBuilder->build()->zAdd(
            sprintf('salesforce_outbound_messages:%s', $channelName),
            [],
            microtime(true),
            sprintf('%s:%s', $notificationId, base64_encode($xmlRequest))
        );

        return $notificationId;
    }
}