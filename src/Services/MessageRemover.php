<?php

namespace App\Services;

use App\Services\Builder\RedisClientBuilder;
use App\Services\Parser\XmlRequestMessageNotificationIdParser;
use App\Services\Validator\MessageNotificationIdValidator;

class MessageRemover
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

    public function remove(string $channelName, string $notificationId): void
    {
        $this->messageNotificationIdValidator->validate($notificationId);

        $redis = $this->redisClientBuilder->build();
        $iterator = null;

        $matches = $redis->zScan(
            sprintf('salesforce_outbound_messages:%s', $channelName),
            $iterator,
            sprintf('%s:*', $notificationId)
        );

        if ($matches) {
            $redis->zRem(
                sprintf('salesforce_outbound_messages:%s', $channelName),
                array_keys($matches)[0]
            );
        }
    }
}