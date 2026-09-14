<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Foundation-stage stub. Real logic (materializing automation_execution_steps
 * from active workflows) is added in the Automation stage, once the
 * events/automation_workflows/automation_steps tables exist. This command
 * exists now so the scheduler wiring (routes/console.php) and the cron entry
 * on Hostinger can be set up and verified end-to-end before any real
 * automation logic is written — i.e. so "is the cron actually firing this"
 * is answered before it matters.
 */
class GenerateScheduledExecutions extends Command
{
    protected $signature = 'automation:generate-executions';

    protected $description = 'Materialize due automation execution steps (foundation stub — no-op until the Automation stage)';

    public function handle(): int
    {
        $this->info('automation:generate-executions ran at '.now()->toDateTimeString().' (stub — no automation tables yet)');

        return self::SUCCESS;
    }
}
