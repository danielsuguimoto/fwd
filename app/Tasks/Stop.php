<?php

namespace App\Tasks;

use App\Builder\DockerCompose;

class Stop extends Task
{
    /** @var bool $purge */
    protected $purge = true;

    /** @var string $services */
    protected $services;

    public function run(...$args): int
    {
        $tasks = [
            [$this, 'destroyContainers'],
        ];

        return $this->runCallables($tasks);
    }

    public function purge(bool $purge): self
    {
        $this->purge = $purge;

        return $this;
    }

    public function services(string $services): self
     {
         $this->services = $services;

         return $this;
     }

    public function destroyContainers(): int
    {
        return $this->runTask('Turning off fwd', function () {
            $args[] = 'rm';
            $args[] = '--stop';
            $args[] = '--force';

            if ($this->purge) {
                $args[] = '-v';
            }

            if (! is_null($this->services)) {
                $args[] = ($this->services ?: env('FWD_START_DEFAULT_SERVICES'));
            }

            $exitCode = $this->runCommandWithoutOutput(
                DockerCompose::makeWithDefaultArgs(...$args),
                false
            );

            if ($exitCode) {
                return 1;
            }

            if ($this->purge && is_null($this->services)) {
                $exitCode = $this->runCommandWithoutOutput(
                    DockerCompose::makeWithDefaultArgs('down', '--remove-orphans'),
                    false
                );

                if ($exitCode) {
                    return 1;
                }
            }

            return 0;
        });
    }
}
