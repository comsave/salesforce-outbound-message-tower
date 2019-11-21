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
        $this->markTestSkipped();
        $accessManager = new AccessManager('REMOTE_ADDR', null);

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
        $accessManager = new AccessManager('REMOTE_ADDR', '13.108.238.128/28');

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->headers = $this->createMock(HeaderBag::class);
        $requestMock->headers->expects($this->never())
            ->method('get');

        $accessManager->parseSubnetsToIpAddresses();

        $this->assertEquals([
            '13.108.238.128',
            '13.108.238.129',
            '13.108.238.130',
            '13.108.238.131',
            '13.108.238.132',
            '13.108.238.133',
            '13.108.238.134',
            '13.108.238.135',
            '13.108.238.136',
            '13.108.238.137',
            '13.108.238.138',
            '13.108.238.139',
            '13.108.238.140',
            '13.108.238.141',
            '13.108.238.142',
            '13.108.238.143',
        ], $accessManager->getAllowedIps());
    }
}