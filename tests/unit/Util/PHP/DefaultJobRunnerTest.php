<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util\PHP;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DefaultJobRunner::class)]
#[UsesClass(Job::class)]
#[UsesClass(Result::class)]
#[Small]
final class DefaultJobRunnerTest extends TestCase
{
    public static function provider(): array
    {
        return [
            'output to stdout' => [
                new Result('test', ''),
                new Job(
                    <<<'EOT'
<?php declare(strict_types=1);
fwrite(STDOUT, 'test');

EOT
                ),
            ],
            'output to stderr' => [
                new Result('', 'test'),
                new Job(
                    <<<'EOT'
<?php declare(strict_types=1);
fwrite(STDERR, 'test');

EOT
                ),
            ],
            'output to stdout and stderr' => [
                new Result('test-stdout', 'test-stderr'),
                new Job(
                    <<<'EOT'
<?php declare(strict_types=1);
fwrite(STDOUT, 'test-stdout');
fwrite(STDERR, 'test-stderr');

EOT
                ),
            ],
            'redirect stderr to stdout' => [
                new Result('test', ''),
                new Job(
                    <<<'EOT'
<?php declare(strict_types=1);
fwrite(STDERR, 'test');

EOT,
                    redirectErrors: true,
                ),
            ],
            'environment variables' => [
                new Result('test', ''),
                new Job(
                    <<<'EOT'
<?php declare(strict_types=1);
print getenv('test');

EOT,
                    environmentVariables: ['test' => 'test'],
                ),
            ],
            'arguments' => [
                new Result('test', ''),
                new Job(
                    <<<'EOT'
<?php declare(strict_types=1);
print $argv[1];

EOT,
                    arguments: ['test'],
                ),
            ],
        ];
    }

    #[DataProvider('provider')]
    public function testRunsJobInSeparateProcess(Result $expected, Job $job): void
    {
        $jobRunner = new DefaultJobRunner;

        $result = $jobRunner->run($job);

        $this->assertSame($expected->stdout(), $result->stdout());
        $this->assertSame($expected->stderr(), $result->stderr());
    }
}
