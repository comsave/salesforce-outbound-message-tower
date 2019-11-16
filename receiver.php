<?php

define('BASE_DIR', dirname(__FILE__));

function get_salesforce_notification_id(string $requestXml): ?string {
    $requestXml = str_ireplace(['soapenv:', 'soap:', 'sf:'], '', $requestXml);
    $simpleXml = new SimpleXMLElement($requestXml);

    return @$simpleXml->Body->notifications->ActionId ?? null;
}

function save_salesforce_notification(string $notificationId, string $requestXml): string {
    $notificationMessageFilePath = sprintf('messages/%s.xml', $notificationId);

    @file_put_contents(BASE_DIR . DIRECTORY_SEPARATOR . $notificationMessageFilePath, $requestXml);

    return $notificationMessageFilePath;
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