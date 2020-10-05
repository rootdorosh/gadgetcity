<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Parser\ParserService;

class Text extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'text:sync';

    /**
     * The console command description.
     * https://op.mos.ru/EHDWSREST/catalog/export/get?id=484577
     *
     * @var string
     */
    protected $description = '';

    /**
     * @var ParserService
     */
    protected $parserService;

    /**
     * Create a new command instance.
     * @param ParserService $parserService
     * @return void
     */
    public function __construct(ParserService $parserService)
    {
        $this->parserService = $parserService;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->parserService->textSync();
    }
}
