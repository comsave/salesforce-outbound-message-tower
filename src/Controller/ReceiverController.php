<?php

namespace App\Controller;

use App\Services\MessageReceiver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceiverController extends AbstractController
{
    public function receiveMessage(Request $request, MessageReceiver $messageReceiver, string $channelName): Response
    {
        $xmlRequest = $request->getContent();
        $notificationId = $messageReceiver->receive($channelName, $xmlRequest);

        return new JsonResponse([
            'status' => 'OK',
            'message' => sprintf('Received notification `%s`.', $notificationId),
        ], Response::HTTP_OK);
    }
}