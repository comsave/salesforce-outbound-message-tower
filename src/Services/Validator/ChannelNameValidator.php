<?php

namespace App\Services\Validator;

use App\Exception\OutboundMessageTowerException;

class ChannelNameValidator
{
    public function validate(?string $channelName): bool
    {
        if (!preg_match('/^[a-z0-9]$/i', $channelName)) {
            throw new OutboundMessageTowerException('Invalid: `channelName`.');
        }

        return true;
    }
}