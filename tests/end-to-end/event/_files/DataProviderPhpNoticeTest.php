<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TestFixture\Event;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DataProviderPhpNoticeTest extends TestCase
{
    public static function values(): array
    {
        $f = static function (): void
        {
        };

        $a = &$f();

        return [[true], [true]];
    }

    #[DataProvider('values')]
    public function testSuccess(bool $value): void
    {
        $this->assertTrue($value);
    }
}
