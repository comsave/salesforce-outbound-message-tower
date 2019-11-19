<?php

namespace App\Controller;

use App\Exception\OutboundMessageTowerException;
use App\Services\NextMessageSelector;
use App\Services\ProcessedMessageRemover;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BroadcasterController extends AbstractController
{
    public function broadcast(string $channelName, NextMessageSelector $nextMessageSelector): Response
    {
        return new Response(
            $nextMessageSelector->nextMessage(), Response::HTTP_OK, [
                'Content-Type' => 'text/xml',
            ]
        );
    }

    public function broadcastProcessed(string $channelName, Request $request, ProcessedMessageRemover $processedMessageRemover): Response
    {
        $notificationId = $request->get('notificationId');

        try {
            $processedMessageRemover->remove($notificationId);
        } catch (OutboundMessageTowerException $ex) {
            return new JsonResponse(
                [
                    'status' => 'Error',
                    'message' => sprintf($ex->getMessage()),
                ], Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            [
                'status' => 'OK',
                'message' => sprintf('Removed processed notification `%s`.', $notificationId),
            ], Response::HTTP_OK
        );
    }
}