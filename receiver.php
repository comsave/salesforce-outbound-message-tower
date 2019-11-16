<?php

require_once 'config.php';

function get_salesforce_notification_id(string $requestXml): ?string {
    $requestXml = str_ireplace(['soapenv:', 'soap:', 'sf:'], '', $requestXml);
    $simpleXml = new SimpleXMLElement($requestXml);

    return @$simpleXml->Body->notifications->ActionId ?? null;
}

function save_salesforce_notification(string $notificationId, string $requestXml): void {
    $notificationMessageFilePath = sprintf('%s/%s.xml', APP_MESSAGE_DIR, $notificationId);

    @file_put_contents($notificationMessageFilePath, $requestXml);
}

$requestXml = file_get_contents('php://input');

$notificationId = get_salesforce_notification_id($requestXml);
save_salesforce_notification($notificationId, $requestXml);

header('Content-Type: application/json');

$response = [
    'status' => 'OK',
    'message' => sprintf('Received notification `%s`.', $notificationId),
];

echo json_encode($response);