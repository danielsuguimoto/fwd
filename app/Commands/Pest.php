<?php

namespace App\Commands;

use App\Builder\Php;
use App\Commands\Traits\HasDynamicArgs;

class Pest extends Command
{
    use HasDynamicArgs;

    /**
     * The name of the command.
     *
     * @var string
     */
    protected $name = 'pest';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run php pest commands in the APP container.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->commandExecutor->run(
            Php::makeWithDefaultArgs('vendor/bin/pest', $this->getArgs())
        );
    }
}
