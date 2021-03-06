<?php

namespace App\Controller;

use App\Exception\OutboundMessageTowerException;
use App\Services\MessageRemover;
use App\Services\NextMessageSelector;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BroadcasterController extends AbstractController
{
    public function broadcast(
        NextMessageSelector $nextMessageSelector,
        LoggerInterface $logger,
        string $channelName
    ): Response {
        try {
            $nextMessage = $nextMessageSelector->nextMessage($channelName);
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

        return new Response($nextMessage, Response::HTTP_OK, [
            'Content-Type' => 'text/xml',
        ]);
    }

    public function broadcastProcessed(
        MessageRemover $processedMessageRemover,
        LoggerInterface $logger,
        string $channelName,
        string $notificationId
    ): Response {
        try {
            $processedMessageRemover->remove($channelName, $notificationId);
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

        return new JsonResponse([
            'status' => 'OK',
            'message' => sprintf('Removed processed notification `%s`.', $notificationId),
        ], Response::HTTP_OK);
    }
}