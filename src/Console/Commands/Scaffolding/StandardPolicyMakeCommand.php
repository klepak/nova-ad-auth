<?php

namespace Klepak\NovaAdAuth\Console\Commands\Scaffolding;

use Illuminate\Foundation\Console\PolicyMakeCommand;

class StandardPolicyMakeCommand extends PolicyMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:std-policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new standard policy class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Policy';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/policy.stub';
    }
}
