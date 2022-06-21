<?php

namespace Twoscore23\LaravelBetterMakes\Commands;

use Illuminate\Console\Command;

class MakeControllerCombo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:controller-combo {name} {columns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a templated Controller & Service';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = (string) $this->argument('name');
        $columns = $this->argument('columns');

        $this->call('make:custom-controller', ["name" => $name . 'Controller', "columns" => $columns]);
        $this->call('make:service', ["name" => $name . 'Service']);
    }
}
