<?php

namespace Twoscore23\LaravelBetterMakes\Commands;

use Illuminate\Console\Command;

class MakeFullModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:full-model {model : Name of new model} {columns} {--plural=true} {--with-controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates everything needed for a new model.';

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
        $isPlural = (bool) $this->option('plural');
        $withController = $this->option('with-controller');
        $columns = $this->argument('columns');
        $modelName = (string) $this->argument('model');
        $splitString = strtolower(preg_replace("([A-Z])", "_$0", lcfirst($modelName)));
        $sqlString = $isPlural 
            ? getPlural($splitString)
            : $splitString;

        $this->call("make:custom-migration", ["name" => "create_" . $sqlString . "_table", "columns" => $columns]);
        $this->call("make:custom-model", ["name" => $modelName, "columns" => $columns]);
        $this->call("make:custom-factory", ["name" => $modelName . "Factory", "columns" => $columns]);
        $this->call("make:custom-resource", ["name" => $modelName . "Resource", "columns" => $columns]);

        if ($withController)
        {
            $this->call("make:controller-combo", ["name" => $modelName, "columns" => $columns]);
        }
    }
}
