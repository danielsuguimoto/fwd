<?php

namespace Tests\Feature;

use App\Checker;
use Tests\TestCase;

class StartTest extends TestCase
{
    public function testStart()
    {
        $this->mockChecker();

        $this->artisan('start')->assertExitCode(0);

        $this->assertDockerCompose('up -d --force-recreate ' . env('FWD_START_DEFAULT_SERVICES'));
        $this->assertDocker('network create --attachable ' . env('FWD_NETWORK'));
    }

    public function testStartWithAll()
    {
        $this->mockChecker();

        $this->artisan('start --all')->assertExitCode(0);

        $this->assertDockerCompose('up -d --force-recreate');
        $this->assertDocker('network create --attachable ' . env('FWD_NETWORK'));
    }

    public function testStartWithSpecificServices()
    {
        $this->mockChecker();

        $this->artisan('start --services=chromedriver')->assertExitCode(0);

        $this->assertDockerCompose('up -d --force-recreate chromedriver');
    }

    public function testStartOldVersionAllDependencies()
    {
        $this->mockChecker(
            true,
            '0.0.0',
            '0.0.0',
            '0.0.0'
        );

        $this->artisan('start')->assertExitCode(1);
    }

    public function testStartOldVersionDockerDependency()
    {
        $this->mockChecker(
            true,
            '0.0.0',
            Checker::DOCKER_API_MIN_VERSION,
            Checker::DOCKER_COMPOSE_MIN_VERSION
        );

        $this->artisan('start')->assertExitCode(1);
    }

    public function testStartOldVersionDockerAPIDependency()
    {
        $this->mockChecker(
            true,
            Checker::DOCKER_MIN_VERSION,
            '0.0.0',
            Checker::DOCKER_COMPOSE_MIN_VERSION
        );

        $this->artisan('start')->assertExitCode(1);
    }

    public function testStartOldVersionDockerComposeDependency()
    {
        $this->mockChecker(
            true,
            Checker::DOCKER_MIN_VERSION,
            Checker::DOCKER_API_MIN_VERSION,
            '0.0.0'
        );

        $this->artisan('start')->assertExitCode(1);
    }

    public function testStartNotRunningDaemon()
    {
        $this->mockChecker(false);

        $this->artisan('start')->assertExitCode(1);
    }

    protected function mockChecker(
        $dockerIsRunning = true,
        $dockerVersion = Checker::DOCKER_MIN_VERSION,
        $dockerApiVersion = Checker::DOCKER_API_MIN_VERSION,
        $dockerComposeVersion = Checker::DOCKER_COMPOSE_MIN_VERSION
    ) {
        $this->mock(Checker::class, function ($mock) use (
            $dockerIsRunning,
            $dockerVersion,
            $dockerApiVersion,
            $dockerComposeVersion
        ) {
            $mock->shouldReceive('checkDockerIsRunning')
                ->andReturn($dockerIsRunning);

            $mock->shouldReceive('dockerVersion')
                ->andReturn($dockerVersion);

            $mock->shouldReceive('dockerApiVersion')
                ->andReturn($dockerApiVersion);

            $mock->shouldReceive('dockerComposeVersion')
                ->andReturn($dockerComposeVersion);
        })->makePartial();
    }
}
