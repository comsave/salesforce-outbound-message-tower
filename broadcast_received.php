<?php

$notificationId = $_GET['notificationId'] ?? null;

if (!$notificationId) {
    die('Not defined: `notificationId`.');
}
else if(!preg_match('/[a-z0-9]{15,18}/i', $notificationId)) {
    die('Invalid: `notificationId`.');
}

require_once 'config.php';

function remove_processed_salesforce_notification(string $notificationId): void {
    $broadcastMessageFile = sprintf('%s/%s.xml', APP_MESSAGE_DIR, $notificationId);

    @unlink($broadcastMessageFile);
}

remove_processed_salesforce_notification($notificationId);

header('Content-Type: application/json');

$response = [
    'status' => 'OK',
    'message' => sprintf('Removed processed notification `%s`.', $notificationId),
];

echo json_encode($response);