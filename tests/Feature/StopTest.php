<?php

namespace Tests\Feature;

use Tests\TestCase;

class StopTest extends TestCase
{
    public function testStop()
    {
        $this->artisan('stop')->assertExitCode(0);

        $this->assertDockerCompose('rm --stop --force ' . env('FWD_START_DEFAULT_SERVICES'));
    }

    public function testStopAndPurge()
    {
        $this->artisan('stop --purge')->assertExitCode(0);

        $this->assertDockerCompose('rm --stop --force -v ' . env('FWD_START_DEFAULT_SERVICES'));
    }

    public function testStopAll()
    {
        $this->artisan('stop --all')->assertExitCode(0);

        $this->assertDockerCompose('rm --stop --force');
    }

    public function testStopAllAndPurge()
    {
        $this->artisan('stop --all --purge')->assertExitCode(0);

        $this->assertDockerCompose('rm --stop --force -v');
        $this->assertDockerCompose('down --remove-orphans');
    }

    public function testStopService()
    {
        $this->artisan('stop --services=chromedriver')->assertExitCode(0);

        $this->assertDockerCompose('rm --stop --force chromedriver');
    }

    public function testStopServiceAndPurge()
    {
        $this->artisan('stop --purge --services=chromedriver')->assertExitCode(0);

        $this->assertDockerCompose('rm --stop --force -v chromedriver');
    }
}
