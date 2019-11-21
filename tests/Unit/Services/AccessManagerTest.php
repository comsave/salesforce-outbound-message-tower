<?php

namespace Tests\Unit\Services;

use App\Services\AccessManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \App\Services\AccessManager
 */
class AccessManagerTest extends TestCase
{
    /**
     * @covers ::isAllowed()
     */
    public function testUserShouldBeAllowedIfNoIpRestrictionsAreSet(): void
    {
        $userIpRequestHeaderName = 'REMOTE_ADDR';
        $accessManager = new AccessManager($userIpRequestHeaderName, null);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->headers = $this->createMock(HeaderBag::class);
        $requestMock->headers->expects($this->never())
            ->method('get');

        $accessManager->isAllowed($requestMock);
    }

    /**
     * @covers ::parseSubnetsToIpAddresses()
     */
    public function testParsesSubnetIpAddressesCorrectly(): void
    {
        $userIpRequestHeaderName = 'REMOTE_ADDR';
        $accessManager = new AccessManager($userIpRequestHeaderName, '13.108.238.128/28');

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->headers = $this->createMock(HeaderBag::class);
        $requestMock->headers->expects($this->never())
            ->method('get');

        $accessManager->parseSubnetsToIpAddresses();

        $this->assertEquals([], $accessManager->getAllowedIps());
    }
}