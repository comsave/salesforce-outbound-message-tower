<?php

namespace App\EventSubscriber;

use App\Services\AccessManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class AccessManagerEventSubscriber implements EventSubscriberInterface
{
    /** @var AccessManager */
    private $accessManager;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(AccessManager $accessManager)
    {
        $this->accessManager = $accessManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['checkIsUserIpAllowed', 200],
            ],
        ];
    }

    public function checkIsUserIpAllowed(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->accessManager->isAllowed($event->getRequest());
    }
}