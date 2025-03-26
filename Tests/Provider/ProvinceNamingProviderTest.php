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

namespace Tests\Sylius\Component\Addressing\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Provider\ProvinceNamingProvider;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class ProvinceNamingProviderTest extends TestCase
{
    /** @var RepositoryInterface&MockObject */
    private MockObject $provinceRepositoryMock;

    private ProvinceNamingProvider $provinceNamingProvider;

    protected function setUp(): void
    {
        $this->provinceRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->provinceNamingProvider = new ProvinceNamingProvider($this->provinceRepositoryMock);
    }

    public function testThrowsInvalidArgumentExceptionWhenProvinceWithGivenCodeIsNotFound(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects($this->once())->method('getProvinceName')->willReturn(null);
        $addressMock->expects($this->once())->method('getProvinceCode')->willReturn('ZZ-TOP');
        $this->provinceRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => 'ZZ-TOP'])->willReturn(null);
        $this->expectException(InvalidArgumentException::class);
        $this->expectException(InvalidArgumentException::class);
        $this->provinceNamingProvider->getAbbreviation($addressMock);
        $this->expectException(InvalidArgumentException::class);
        $this->provinceNamingProvider->getAbbreviation($addressMock);
    }

    public function testGetsProvinceNameIfProvinceWithGivenCodeExistInDatabase(): void
    {
        /** @var ProvinceInterface&MockObject $provinceMock */
        $provinceMock = $this->createMock(ProvinceInterface::class);
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects($this->once())->method('getProvinceCode')->willReturn('IE-UL');
        $addressMock->expects($this->once())->method('getProvinceName')->willReturn(null);
        $this->provinceRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => 'IE-UL'])->willReturn($provinceMock);
        $provinceMock->expects($this->once())->method('getName')->willReturn('Ulster');
        self::assertSame('Ulster', $this->provinceNamingProvider->getName($addressMock));
    }

    public function testGetsProvinceNameFormAddress(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects($this->once())->method('getProvinceName')->willReturn('Ulster');
        self::assertSame('Ulster', $this->provinceNamingProvider->getName($addressMock));
    }

    public function testReturnsNothingIfProvinceNameAndCodeAreNotGivenInAnAddress(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects($this->once())->method('getProvinceCode')->willReturn(null);
        $addressMock->expects($this->once())->method('getProvinceName')->willReturn(null);
        self::assertSame('', $this->provinceNamingProvider->getName($addressMock));
        self::assertSame('', $this->provinceNamingProvider->getAbbreviation($addressMock));
    }

    public function testGetsProvinceAbbreviationByItsCodeIfProvinceExistsInDatabase(): void
    {
        /** @var ProvinceInterface&MockObject $provinceMock */
        $provinceMock = $this->createMock(ProvinceInterface::class);
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects($this->once())->method('getProvinceName')->willReturn(null);
        $addressMock->expects($this->once())->method('getProvinceCode')->willReturn('IE-UL');
        $this->provinceRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => 'IE-UL'])->willReturn($provinceMock);
        $provinceMock->expects($this->once())->method('getAbbreviation')->willReturn('ULS');
        self::assertSame('ULS', $this->provinceNamingProvider->getAbbreviation($addressMock));
    }

    public function testGetsProvinceNameIfItsAbbreviationIsNotSetButProvinceExistsInDatabase(): void
    {
        /** @var ProvinceInterface&MockObject $provinceMock */
        $provinceMock = $this->createMock(ProvinceInterface::class);
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects($this->once())->method('getProvinceName')->willReturn(null);
        $addressMock->expects($this->once())->method('getProvinceCode')->willReturn('IE-UL');
        $this->provinceRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => 'IE-UL'])->willReturn($provinceMock);
        $provinceMock->expects($this->once())->method('getAbbreviation')->willReturn(null);
        $provinceMock->expects($this->once())->method('getName')->willReturn('Ulster');
        self::assertSame('Ulster', $this->provinceNamingProvider->getAbbreviation($addressMock));
    }

    public function testGetsProvinceNameFormAddressIfItsAbbreviationIsNotSet(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects($this->once())->method('getProvinceName')->willReturn('Ulster');
        self::assertSame('Ulster', $this->provinceNamingProvider->getAbbreviation($addressMock));
    }
}
