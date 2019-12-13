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
    public function __construct(string $userIpRequestHeaderName, ?string $allowedIps = '')
    {
        $this->userIpRequestHeaderName = $userIpRequestHeaderName;

        $this->allowedIps = array_filter(explode(',', $allowedIps));

        $this->parseSubnetsToIpAddresses();
    }

    public function parseSubnetsToIpAddresses(): void
    {
        $parsedIpAddresses = [];

        foreach ($this->getAllowedIps() as $allowedIpKey => $allowedIp) {
            list($subnetIp, $networkSize) = explode('/', $allowedIp . '/');

            if ($networkSize > 0) {
                unset($this->allowedIps[$allowedIpKey]);

                $subnet = new SubnetCalculator($subnetIp, (int)$networkSize);

                foreach ($subnet->getAllIPAddresses() as $ipAddress) {
                    $parsedIpAddresses[] = $ipAddress;
                }
            }
        }

        $this->allowedIps = $this->getAllowedIps() + $parsedIpAddresses;
    }

    public function getAllowedIps(): array
    {
        return $this->allowedIps;
    }

    public function isAllowed(Request $request): void
    {
        if (!$this->getAllowedIps() || !is_array($this->getAllowedIps()) || count($this->getAllowedIps()) <= 0) {
            return;
        }

        $userIp = $request->headers->get($this->userIpRequestHeaderName);

        if (!$userIp) {
            throw new OutboundMessageTowerException(sprintf('User IP is not set in request header `%s`.',
                $this->userIpRequestHeaderName));
        }

        if (!in_array($userIp, $this->getAllowedIps())) {
            throw new OutboundMessageTowerException(sprintf('User IP `%s` is no granted access.', $userIp));
        }
    }
}