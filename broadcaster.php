<?php

require_once 'config.php';

function get_next_broadcast_message_file(): ?string {
    $messageFiles = glob(sprintf('%s/*.xml', APP_MESSAGE_DIR));

    usort($messageFiles, function(string $messageFileA, string $messageFileB) {
        return filemtime($messageFileA) > filemtime($messageFileB);
    });

    return $messageFiles[0] ?? null;
}

$broadcastMessageFile = get_next_broadcast_message_file();

header('Content-Type: text/xml');
echo file_get_contents($broadcastMessageFile);