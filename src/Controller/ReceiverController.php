<?php

namespace App\Controller;

use App\Exception\OutboundMessageTowerException;
use App\Services\MessageReceiver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ReceiverController extends AbstractController
{
    public function receiveMessage(Request $request, MessageReceiver $messageReceiver, string $channelName): Response
    {
        $xmlRequest = $request->getContent();

        try {
            $notificationId = $messageReceiver->receive($channelName, $xmlRequest);
        } catch (OutboundMessageTowerException $ex) {
            return new JsonResponse([
                'status' => 'Error',
                'message' => sprintf($ex->getMessage()),
            ], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $ex) {
            return new JsonResponse([
                'status' => 'Error',
                'message' => 'Failed unexpectedly. Look for details in the logs.',
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'status' => 'OK',
            'message' => sprintf('Received notification `%s`.', $notificationId),
        ], Response::HTTP_OK);
    }
}