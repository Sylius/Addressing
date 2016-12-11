<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class Scope
{
    const SHIPPING = 'shipping';
    const TAX = 'tax';
    const ALL = 'all';

    private function __construct()
    {
    }
}
