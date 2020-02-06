<?php

namespace App\Controller;

use App\Exception\OutboundMessageTowerException;
use App\Services\MessageReceiver;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ReceiverController extends AbstractController
{
    public const ACK_RESPONSE = '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope
  xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
  xmlns:ns1="http://soap.sforce.com/2005/09/outbound">
  <SOAP-ENV:Body>
    <ns1:notificationsResponse>
      <ns1:Ack>true</ns1:Ack>
    </ns1:notificationsResponse>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

    public function receiveMessage(
        Request $request,
        MessageReceiver $messageReceiver,
        LoggerInterface $logger,
        string $channelName
    ): Response {
        $xmlRequest = $request->getContent();

        try {
//            $notificationId = $messageReceiver->receive($channelName, $xmlRequest);
            $messageReceiver->receive($channelName, $xmlRequest);
        } catch (OutboundMessageTowerException $ex) {
            return new JsonResponse([
                'status' => 'Error',
                'message' => sprintf($ex->getMessage()),
            ], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $ex) {
            $logger->critical($ex->getMessage(), $ex->getTrace());

            return new JsonResponse([
                'status' => 'Error',
                'message' => 'Failed unexpectedly. Look for details in the logs.',
            ], Response::HTTP_BAD_REQUEST);
        }

        return new Response(static::ACK_RESPONSE);
//        return new JsonResponse([
//            'status' => 'OK',
//            'message' => sprintf('Received notification `%s`.', $notificationId),
//        ], Response::HTTP_OK);
    }
}