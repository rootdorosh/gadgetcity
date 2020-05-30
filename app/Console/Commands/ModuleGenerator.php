<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ModuleGenerator\ModuleGeneratorService;

class ModuleGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:gen {name} {--force}';

    /**
     * The console command description.
     * https://op.mos.ru/EHDWSREST/catalog/export/get?id=484577
     *
     * @var string
     */
    protected $description = 'Module Generator';

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
        $name = $this->argument('name');
        $force = $this->option('force');
        
       (new ModuleGeneratorService($name, $force))->run(); 
    }
}
