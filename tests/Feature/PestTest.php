<?php

namespace Tests\Feature;

use App\Environment;
use Tests\TestCase;

class PestTest extends TestCase
{
    public function pestTest()
    {
        $this->artisan('pest')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/pest');
    }

    public function pestTestWithGroup()
    {
        $this->artisan('pest --group=something')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/pest --group=something');
    }

    public function pestTestWithCoverage()
    {
        $this->artisan('pest --coverage')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/pest --coverage');
    }

    public function pestTestingWithDockerComposeFlags()
    {
        app(Environment::class)->overloadEnv('tests/fixtures/.env.docker-compose_exec');

        $this->artisan('pest')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/pest');
    }
}
