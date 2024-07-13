<?php

namespace Modules\Core\Traits;

use Symfony\Component\Console\Command\Command;

trait AutoRegistersCommands
{
    /**
     * Automatically registers console commands for a module.
     *
     * @param  string  $moduleName  The name of the module.
     */
    protected function autoRegisterCommands(string $moduleName): void
    {
        $commandsDirectory = module_path($moduleName, 'Console/Commands');
        $namespace = 'Modules\\'.$moduleName.'\\Console\\Commands\\';

        $commandFiles = glob($commandsDirectory.'/*.php');

        if ($commandFiles !== false) {
            foreach ($commandFiles as $commandFile) {
                $commandClassName = basename($commandFile, '.php');
                $commandClass = $namespace.$commandClassName;

                if (is_subclass_of($commandClass, Command::class)) {
                    $this->commands([$commandClass]);
                }
            }
        }
    }
}
