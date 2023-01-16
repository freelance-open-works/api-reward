<?php

namespace App\Console\Commands;

use App\Http\Controllers\User\MainController;
use Illuminate\Console\Command;

class DailyRefreshKuliaLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kuliahLog:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh kuliah log everyday so the points get refreshed without opening the mobile app.';

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
     * @return int
     */
    public function handle()
    {
        $main = new MainController();
        $main->generateSaveLogKuliahAll();
    }
}
