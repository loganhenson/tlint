<?php

namespace Tests\Linting;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tighten\TLint\Commands\LintCommand;

class LaravelPresetPassesForFreshInstallTest extends TestCase
{
    /** @test */
    public function no_lints_when_using_laravel_preset()
    {
        $application = new Application;
        $command = new LintCommand(__DIR__ . '/../fixtures/laravel/');
        $application->add($command);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command'  => $command->getName(),
            'file or directory' => '.',
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
