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

namespace Tests\Sylius\Component\Addressing\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Checker\CountryProvincesDeletionChecker;
use Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class CountryProvincesDeletionCheckerTest extends TestCase
{
    /** @var RepositoryInterface&MockObject */
    private MockObject $zoneMemberRepositoryMock;

    /** @var RepositoryInterface&MockObject */
    private MockObject $provinceRepositoryMock;

    private CountryProvincesDeletionChecker $countryProvincesDeletionChecker;

    protected function setUp(): void
    {
        $this->zoneMemberRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->provinceRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->countryProvincesDeletionChecker = new CountryProvincesDeletionChecker($this->zoneMemberRepositoryMock, $this->provinceRepositoryMock);
    }

    public function testImplementsCountryProvincesDeletionCheckerInterface(): void
    {
        self::assertInstanceOf(CountryProvincesDeletionCheckerInterface::class, $this->countryProvincesDeletionChecker);
    }

    public function testSaysProvincesWithinACountryAreNotDeletableIfThereIsAProvinceThatExistsAsAZoneMember(): void
    {
        /** @var CountryInterface&MockObject $countryMock */
        $countryMock = $this->createMock(CountryInterface::class);
        /** @var ProvinceInterface&MockObject $firstProvinceMock */
        $firstProvinceMock = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $secondProvinceMock */
        $secondProvinceMock = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $thirdProvinceMock */
        $thirdProvinceMock = $this->createMock(ProvinceInterface::class);
        /** @var ZoneMemberInterface&MockObject $zoneMemberMock */
        $zoneMemberMock = $this->createMock(ZoneMemberInterface::class);
        $firstProvinceMock->expects($this->once())->method('getCode')->willReturn('US-AK');
        $secondProvinceMock->expects($this->once())->method('getCode')->willReturn('US-TX');
        $thirdProvinceMock->expects($this->once())->method('getCode')->willReturn('US-KY');
        $countryMock->expects($this->once())->method('getProvinces')->willReturn(new ArrayCollection([$secondProvinceMock]));
        $this->provinceRepositoryMock->expects($this->once())->method('findBy')->with(['country' => $countryMock])->willReturn([
            $firstProvinceMock,
            $secondProvinceMock,
            $thirdProvinceMock,
        ]);
        $this->zoneMemberRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => [0 => 'US-AK', 2 => 'US-KY']])
            ->willReturn($zoneMemberMock)
        ;
        self::assertFalse($this->countryProvincesDeletionChecker->isDeletable($countryMock));
    }

    public function testSaysProvincesWithinACountryAreDeletableIfThereIsNotAProvinceThatExistsAsAZoneMember(): void
    {
        /** @var CountryInterface&MockObject $countryMock */
        $countryMock = $this->createMock(CountryInterface::class);
        /** @var ProvinceInterface&MockObject $firstProvinceMock */
        $firstProvinceMock = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $secondProvinceMock */
        $secondProvinceMock = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $thirdProvinceMock */
        $thirdProvinceMock = $this->createMock(ProvinceInterface::class);
        $firstProvinceMock->expects($this->once())->method('getCode')->willReturn('US-AK');
        $secondProvinceMock->expects($this->once())->method('getCode')->willReturn('US-TX');
        $thirdProvinceMock->expects($this->once())->method('getCode')->willReturn('US-KY');
        $countryMock->expects($this->once())->method('getProvinces')->willReturn(new ArrayCollection([$secondProvinceMock]));
        $this->provinceRepositoryMock->expects($this->once())->method('findBy')->with(['country' => $countryMock])->willReturn([
            $firstProvinceMock,
            $secondProvinceMock,
            $thirdProvinceMock,
        ]);
        $this->zoneMemberRepositoryMock->expects($this->once())->method('findOneBy')->with(['code' => [0 => 'US-AK', 2 => 'US-KY']])
            ->willReturn(null)
        ;
        self::assertTrue($this->countryProvincesDeletionChecker->isDeletable($countryMock));
    }
}
