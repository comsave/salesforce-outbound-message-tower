<?php

namespace App\Controller;

use App\Services\MessageReceiver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceiverController extends AbstractController
{
    public function receiveMessage(string $channelName, Request $request, MessageReceiver $messageReceiver): Response
    {
        $notificationId = $messageReceiver->receive($request->getContent());

        return new JsonResponse([
            'status' => 'OK',
            'message' => sprintf('Received notification `%s`.', $notificationId),
        ], Response::HTTP_OK);
    }
}