<?php

namespace App\Console\Commands;

use App\Services\Visitors\ExpireVisitors;
use Illuminate\Console\Command;

class ExpireVisitorsCommand extends Command
{
    protected $signature = 'visitors:expire';

    protected $description = 'Expire pending or approved visitors whose validity has ended.';

    public function handle(ExpireVisitors $expireVisitors): int
    {
        $count = $expireVisitors->run();

        $this->info("Expired {$count} visitor record(s).");

        return self::SUCCESS;
    }
}
