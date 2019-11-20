<?php

namespace App\Services\Parser;

use App\Exception\OutboundMessageTowerException;

class XmlRequestMessageNotificationIdParser
{
    public function parse(string $xmlRequest): string
    {
        $notificaitonId = $this->getSalesforceNotificationId($xmlRequest);

        if(!$notificaitonId) {
            throw new OutboundMessageTowerException('Failed to parse `notificationId`.');
        }

        return $notificaitonId;
    }

    private function getSalesforceNotificationId(string $xmlRequest): ?string
    {
        $xmlRequest = str_ireplace(['soapenv:', 'soap:', 'sf:'], '', $xmlRequest);
        $simpleXml = new \SimpleXMLElement($xmlRequest);

        return @$simpleXml->Body->notifications->ActionId ?? null;
    }
}