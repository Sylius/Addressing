<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Component\Addressing\Matcher;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Matcher\ZoneMatcher;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;

final class ZoneMatcherTest extends TestCase
{
    /** @var ZoneRepositoryInterface&MockObject */
    private MockObject $zoneRepositoryMock;

    private ZoneMatcher $zoneMatcher;

    protected function setUp(): void
    {
        $this->zoneRepositoryMock = $this->createMock(ZoneRepositoryInterface::class);
        $this->zoneMatcher = new ZoneMatcher($this->zoneRepositoryMock);
    }

    public function testReturnsAMatchingZoneByProvince(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        $this->zoneRepositoryMock->expects($this->once())->method('findOneByAddressAndType')->with($addressMock, ZoneInterface::TYPE_PROVINCE, null)->willReturn($zoneMock);
        self::assertSame($zoneMock, $this->zoneMatcher->match($addressMock));
    }

    public function testReturnsAMatchingZoneByCountry(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        $this->zoneRepositoryMock->expects($this->once())->method('findOneByAddressAndType')->with($addressMock, ZoneInterface::TYPE_PROVINCE, null)->willReturn(null);
        $this->zoneRepositoryMock->expects($this->once())->method('findOneByAddressAndType')->with($addressMock, ZoneInterface::TYPE_COUNTRY, null)->willReturn($zoneMock);
        self::assertSame($zoneMock, $this->zoneMatcher->match($addressMock));
    }

    public function testReturnsAMatchingZoneByMember(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        $this->zoneRepositoryMock->expects($this->once())->method('findOneByAddressAndType')->with($addressMock, ZoneInterface::TYPE_PROVINCE, null)->willReturn(null);
        $this->zoneRepositoryMock->expects($this->once())->method('findOneByAddressAndType')->with($addressMock, ZoneInterface::TYPE_COUNTRY, null)->willReturn(null);
        $this->zoneRepositoryMock->expects($this->once())->method('findOneByAddressAndType')->with($addressMock, ZoneInterface::TYPE_ZONE, null)->willReturn($zoneMock);
        self::assertSame($zoneMock, $this->zoneMatcher->match($addressMock));
    }

    public function testReturnsNullIfNoMatchingZoneFound(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $this->zoneRepositoryMock->expects($this->once())->method('findOneByAddressAndType')->with($addressMock, ZoneInterface::TYPE_PROVINCE, null)->willReturn(null);
        $this->zoneRepositoryMock->expects($this->once())->method('findOneByAddressAndType')->with($addressMock, ZoneInterface::TYPE_COUNTRY, null)->willReturn(null);
        $this->zoneRepositoryMock->expects($this->once())->method('findOneByAddressAndType')->with($addressMock, ZoneInterface::TYPE_ZONE, null)->willReturn(null);
        self::assertNull($this->zoneMatcher->match($addressMock));
    }

    public function testReturnsAllMatchingZones(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ZoneInterface&MockObject $zoneOneMock */
        $zoneOneMock = $this->createMock(ZoneInterface::class);
        /** @var ZoneInterface&MockObject $zoneTwoMock */
        $zoneTwoMock = $this->createMock(ZoneInterface::class);
        /** @var ZoneInterface&MockObject $zoneThreeMock */
        $zoneThreeMock = $this->createMock(ZoneInterface::class);
        $this->zoneRepositoryMock->expects($this->once())->method('findByAddress')->with($addressMock)->willReturn([$zoneOneMock]);
        $this->zoneRepositoryMock->expects($this->once())->method('findByMembers')->with([$zoneOneMock])->willReturn([$zoneTwoMock]);
        $this->zoneRepositoryMock->expects($this->once())->method('findByMembers')->with([$zoneTwoMock])->willReturn([$zoneThreeMock]);
        $this->zoneRepositoryMock->expects($this->once())->method('findByMembers')->with([$zoneThreeMock])->willReturn([]);
        $matchedZones = $this->zoneMatcher->matchAll($addressMock);
        self::assertCount(3, $matchedZones);
        self::assertSame([$zoneOneMock, $zoneTwoMock, $zoneThreeMock], $matchedZones);
    }

    public function testReturnsAllMatchingZonesWithingAMatchingScope(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ZoneInterface&MockObject $zoneOneMock */
        $zoneOneMock = $this->createMock(ZoneInterface::class);
        /** @var ZoneInterface&MockObject $zoneTwoMock */
        $zoneTwoMock = $this->createMock(ZoneInterface::class);
        /** @var ZoneInterface&MockObject $zoneThreeMock */
        $zoneThreeMock = $this->createMock(ZoneInterface::class);
        $zoneOneMock->expects($this->once())->method('getScope')->willReturn('shipping');
        $zoneTwoMock->expects($this->once())->method('getScope')->willReturn(Scope::ALL);
        $zoneThreeMock->expects($this->once())->method('getScope')->willReturn('custom');
        $this->zoneRepositoryMock->expects($this->once())->method('findByAddress')->with($addressMock)->willReturn([$zoneOneMock]);
        $this->zoneRepositoryMock->expects($this->once())->method('findByMembers')->with([$zoneOneMock])->willReturn([$zoneTwoMock]);
        $this->zoneRepositoryMock->expects($this->once())->method('findByMembers')->with([$zoneTwoMock])->willReturn([$zoneThreeMock]);
        $this->zoneRepositoryMock->expects($this->once())->method('findByMembers')->with([$zoneThreeMock])->willReturn([]);
        $matchedZones = $this->zoneMatcher->matchAll($addressMock, 'shipping');
        self::assertCount(2, $matchedZones);
        self::assertSame([$zoneOneMock, $zoneTwoMock], $matchedZones);
    }
}
