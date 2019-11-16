<?php

namespace App\Services;

use SimpleXMLElement;

class MessageReceiver
{
    /** @var string */
    private $broadcastMessagesDir;

    public function __construct(string $broadcastMessagesDir)
    {
        $this->broadcastMessagesDir = $broadcastMessagesDir;
    }

    public function receive(string $requestXml): string
    {
        $notificationId = $this->getSalesforceNotificationId($requestXml);

        $this->saveSalesforceNotification($notificationId, $requestXml);

        return $notificationId;
    }

    private function getSalesforceNotificationId(string $requestXml): ?string
    {
        $requestXml = str_ireplace(['soapenv:', 'soap:', 'sf:'], '', $requestXml);
        $simpleXml = new SimpleXMLElement($requestXml);

        return @$simpleXml->Body->notifications->ActionId ?? null;
    }

    private function saveSalesforceNotification(string $notificationId, string $requestXml): void
    {
        $broadcastMessageFile = sprintf('%s/%s.xml', $this->broadcastMessagesDir, $notificationId);

        @file_put_contents($broadcastMessageFile, $requestXml);
    }
}