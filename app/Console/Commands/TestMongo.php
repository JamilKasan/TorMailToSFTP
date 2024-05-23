<?php

namespace App\Console\Commands;

use App\Models\MailLog;
use Illuminate\Console\Command;

class TestMongo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        MailLog::query()->insert(['test' => 'test']);
    }
}
