<?php

namespace App\Validator;

use App\Exception\OutboundMessageTowerException;

class MessageNotificationIdValidator
{
    public function validate(?string $notificationId): bool
    {
        if (!preg_match('/[a-z0-9]{15,18}/i', $notificationId)) {
            throw new OutboundMessageTowerException('Invalid: `notificationId`.');
        }

        return true;
    }
}