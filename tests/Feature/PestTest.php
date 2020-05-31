<?php

namespace Tests\Feature;

use App\Environment;
use Tests\TestCase;

class PestTest extends TestCase
{
    public function testPest()
    {
        $this->artisan('pest')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/pest');
    }

    public function testPestWithGroup()
    {
        $this->artisan('pest --group=something')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/pest --group=something');
    }

    public function testPestWithCoverage()
    {
        $this->artisan('pest --coverage')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/pest --coverage');
    }

    public function testPestingWithDockerComposeFlags()
    {
        app(Environment::class)->overloadEnv('tests/fixtures/.env.docker-compose_exec');

        $this->artisan('pest')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/pest');
    }
}
