<?php

namespace Klepak\NovaAdAuth\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\AuthServiceProvider;
use Klepak\NovaAdAuth\Policies\StandardPolicy;

class VerifyPoliciesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:verify-policies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for permissions defined in policies but not in config.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $app = app();

        $authServiceProvider = $app->makeWith(AuthServiceProvider::class, [
            'app' => $app
        ]);

        $authModuleConfig = config('auth-roles.permissions');

        $missingNodes = [];
        foreach($authModuleConfig as $guard => $guardPermissions)
        {
            if(!isset($missingNodes[$guard]))
                $missingNodes[$guard] = [];

            foreach($authServiceProvider->policies() as $modelClassName => $policyClassName)
            {
                $policyClass = new $policyClassName();

                if($policyClass instanceof StandardPolicy)
                {
                    foreach($policyClass->getAllNodes() as $node)
                    {
                        if(!in_array($node, $guardPermissions))
                            $missingNodes[$guard][] = $node;
                    }
                }
            }
        }

        foreach($missingNodes as $guard => $guardMissingNodes)
        {
            $this->line('');

            $this->comment("[$guard]");
            if(!empty($guardMissingNodes))
                $this->info('\''.implode('\',\'', $guardMissingNodes).'\',');
            else
                $this->info('No missing nodes');

            $this->line('');
        }
    }
}
