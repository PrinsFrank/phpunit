<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\DataProvider;

use const PHP_EOL;
use PHPUnit\Event\AbstractEventTestCase;
use PHPUnit\Event\Code\ClassMethod;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;

#[CoversClass(PhpWarningTriggered::class)]
#[Small]
final class PhpWarningTriggeredTest extends AbstractEventTestCase
{
    public function testConstructorSetsValues(): void
    {
        $telemetryInfo     = $this->telemetryInfo();
        $dataProvider      = new ClassMethod('the-class', 'the-method');
        $message           = 'message';
        $file              = 'file';
        $line              = 1;
        $suppressed        = false;
        $ignoredByBaseline = false;

        $event = new PhpWarningTriggered(
            $telemetryInfo,
            $dataProvider,
            $message,
            $file,
            $line,
            $suppressed,
            $ignoredByBaseline,
        );

        $this->assertSame($telemetryInfo, $event->telemetryInfo());
        $this->assertSame($dataProvider, $event->dataProvider());
        $this->assertSame($message, $event->message());
        $this->assertSame($file, $event->file());
        $this->assertSame($line, $event->line());
        $this->assertSame($suppressed, $event->wasSuppressed());
        $this->assertSame($ignoredByBaseline, $event->ignoredByBaseline());
        $this->assertSame('Data Provider Triggered PHP Warning (the-class::the-method)' . PHP_EOL . 'message', $event->asString());
    }

    public function testCanBeIgnoredByBaseline(): void
    {
        $event = new PhpWarningTriggered(
            $this->telemetryInfo(),
            new ClassMethod('the-class', 'the-method'),
            'message',
            'file',
            1,
            false,
            true,
        );

        $this->assertTrue($event->ignoredByBaseline());
        $this->assertSame('Data Provider Triggered PHP Warning (the-class::the-method, ignored by baseline)' . PHP_EOL . 'message', $event->asString());
    }

    public function testCanBeSuppressed(): void
    {
        $event = new PhpWarningTriggered(
            $this->telemetryInfo(),
            new ClassMethod('the-class', 'the-method'),
            'message',
            'file',
            1,
            true,
            false,
        );

        $this->assertTrue($event->wasSuppressed());
        $this->assertSame('Data Provider Triggered PHP Warning (the-class::the-method, suppressed using operator)' . PHP_EOL . 'message', $event->asString());
    }
}
