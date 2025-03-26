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

namespace Tests\Sylius\Component\Addressing\Comparator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Comparator\AddressComparator;
use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

final class AddressComparatorTest extends TestCase
{
    private AddressComparator $addressComparator;

    protected function setUp(): void
    {
        $this->addressComparator = new AddressComparator();
    }

    public function testImplementsAddressComparatorInterface(): void
    {
        self::assertInstanceOf(AddressComparatorInterface::class, $this->addressComparator);
    }

    public function testReturnsFalseIfAddressesDiffer(): void
    {
        /** @var AddressInterface&MockObject $firstAddressMock */
        $firstAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface&MockObject $secondAddressMock */
        $secondAddressMock = $this->createMock(AddressInterface::class);
        $firstAddressMock->expects($this->once())->method('getCity')->willReturn('Stoke-On-Trent');
        $firstAddressMock->expects($this->once())->method('getStreet')->willReturn('Villiers St');
        $firstAddressMock->expects($this->once())->method('getCompany')->willReturn('Pizzeria');
        $firstAddressMock->expects($this->once())->method('getPostcode')->willReturn('ST3 4HB');
        $firstAddressMock->expects($this->once())->method('getLastName')->willReturn('Johnson');
        $firstAddressMock->expects($this->once())->method('getFirstName')->willReturn('Gerald');
        $firstAddressMock->expects($this->once())->method('getPhoneNumber')->willReturn('000');
        $firstAddressMock->expects($this->once())->method('getCountryCode')->willReturn('UK');
        $firstAddressMock->expects($this->once())->method('getProvinceCode')->willReturn('UK-WestMidlands');
        $firstAddressMock->expects($this->once())->method('getProvinceName')->willReturn(null);
        $secondAddressMock->expects($this->once())->method('getCity')->willReturn('Toowoomba');
        $secondAddressMock->expects($this->once())->method('getStreet')->willReturn('Ryans Dr');
        $secondAddressMock->expects($this->once())->method('getCompany')->willReturn('Burger');
        $secondAddressMock->expects($this->once())->method('getPostcode')->willReturn('4350');
        $secondAddressMock->expects($this->once())->method('getLastName')->willReturn('Jones');
        $secondAddressMock->expects($this->once())->method('getFirstName')->willReturn('Mia');
        $secondAddressMock->expects($this->once())->method('getPhoneNumber')->willReturn('999');
        $secondAddressMock->expects($this->once())->method('getCountryCode')->willReturn('AU');
        $secondAddressMock->expects($this->once())->method('getProvinceCode')->willReturn(null);
        $secondAddressMock->expects($this->once())->method('getProvinceName')->willReturn('Queensland');
        self::assertFalse($this->addressComparator->equal($firstAddressMock, $secondAddressMock));
    }

    public function testReturnsTrueWhenAddressesAreTheSame(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects($this->once())->method('getCity')->willReturn('Toowoomba');
        $addressMock->expects($this->once())->method('getStreet')->willReturn('Ryans Dr');
        $addressMock->expects($this->once())->method('getCompany')->willReturn('Burger');
        $addressMock->expects($this->once())->method('getPostcode')->willReturn('4350');
        $addressMock->expects($this->once())->method('getLastName')->willReturn('Jones');
        $addressMock->expects($this->once())->method('getFirstName')->willReturn('Mia');
        $addressMock->expects($this->once())->method('getPhoneNumber')->willReturn('999');
        $addressMock->expects($this->once())->method('getCountryCode')->willReturn('AU');
        $addressMock->expects($this->once())->method('getProvinceCode')->willReturn(null);
        $addressMock->expects($this->once())->method('getProvinceName')->willReturn('Queensland');
        self::assertTrue($this->addressComparator->equal($addressMock, $addressMock));
    }

    public function testIgnoresLeadingAndTrailingSpacesOrLetterCases(): void
    {
        /** @var AddressInterface&MockObject $firstAddressMock */
        $firstAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface&MockObject $secondAddressMock */
        $secondAddressMock = $this->createMock(AddressInterface::class);
        $firstAddressMock->expects($this->once())->method('getCity')->willReturn('TOOWOOMBA');
        $firstAddressMock->expects($this->once())->method('getStreet')->willReturn('Ryans Dr     ');
        $firstAddressMock->expects($this->once())->method('getCompany')->willReturn('   Burger');
        $firstAddressMock->expects($this->once())->method('getPostcode')->willReturn(' 4350 ');
        $firstAddressMock->expects($this->once())->method('getLastName')->willReturn('jones ');
        $firstAddressMock->expects($this->once())->method('getFirstName')->willReturn('mIa');
        $firstAddressMock->expects($this->once())->method('getPhoneNumber')->willReturn(' 999');
        $firstAddressMock->expects($this->once())->method('getCountryCode')->willReturn('au');
        $firstAddressMock->expects($this->once())->method('getProvinceCode')->willReturn(null);
        $firstAddressMock->expects($this->once())->method('getProvinceName')->willReturn('qUEENSLAND');
        $secondAddressMock->expects($this->once())->method('getCity')->willReturn('Toowoomba');
        $secondAddressMock->expects($this->once())->method('getStreet')->willReturn('Ryans Dr');
        $secondAddressMock->expects($this->once())->method('getCompany')->willReturn('Burger');
        $secondAddressMock->expects($this->once())->method('getPostcode')->willReturn('4350');
        $secondAddressMock->expects($this->once())->method('getLastName')->willReturn('Jones');
        $secondAddressMock->expects($this->once())->method('getFirstName')->willReturn('Mia');
        $secondAddressMock->expects($this->once())->method('getPhoneNumber')->willReturn('999');
        $secondAddressMock->expects($this->once())->method('getCountryCode')->willReturn('AU');
        $secondAddressMock->expects($this->once())->method('getProvinceCode')->willReturn(null);
        $secondAddressMock->expects($this->once())->method('getProvinceName')->willReturn('Queensland');
        self::assertTrue($this->addressComparator->equal($firstAddressMock, $secondAddressMock));
    }
}
