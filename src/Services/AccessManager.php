<?php

namespace App\Services;

use App\Exception\OutboundMessageTowerException;
use IPv4\SubnetCalculator;
use Symfony\Component\HttpFoundation\Request;

class AccessManager
{
    /** @var string */
    private $userIpRequestHeaderName;

    /** @var array */
    private $allowedIps;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(string $userIpRequestHeaderName, string $salesforceServerIps, string $towerClientIps)
    {
        $this->userIpRequestHeaderName = $userIpRequestHeaderName;

        $this->parseSubnetsToIpAddresses(array_merge(
            explode(',', $salesforceServerIps),
            explode(',', $towerClientIps)
        ));
    }

    public function isAllowed(Request $request): array
    {
        $userIp = $request->headers->get($this->userIpRequestHeaderName);

        if(!$userIp) {
            throw new OutboundMessageTowerException(sprintf('User IP is not set in request header `%s`.', $this->userIpRequestHeaderName));
        }

        return in_array($userIp, $this->allowedIps);
    }

    private function parseSubnetsToIpAddresses(array $allowedIps): void
    {
        foreach($allowedIps as $allowedIp) {
            list($ip, $networkSize) = explode('/', $allowedIp);

            if($networkSize > 0) {
                $subnet = new SubnetCalculator($ip, (int)$networkSize);

                foreach($subnet->getAllIPAddresses() as $subnetIp) {
                    $this->allowedIps[] = $subnetIp;
                }
            }
            else {
                $this->allowedIps = $allowedIp;
            }
        }
    }
}