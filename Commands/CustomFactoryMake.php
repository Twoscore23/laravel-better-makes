<?php

namespace Twoscore23\LaravelBetterMakes\Commands;

use Exception;
use Illuminate\Console\Command;

class CustomFactoryMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom-factory {name} {columns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Factory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $factory = $this->argument('name');
        $columns = explode(',', $this->argument('columns'));

        $factoriesDir = base_path() . '/database/factories';
        $file = $factoriesDir . "/${factory}.php";

        $factoryFields = '';
        foreach ($columns as $index=>$col)
        {
            $factoryFields .= '"' . $col . '" => ';
            $relationship = getRelationship($col);

            if ($relationship)
            {
                $relationship = formatSnakeCaseToStartCase($relationship);
                $factoryFields .= "\App\Models\\$relationship::factory(),";
            }
            else
            {
                $factoryFields .= '$this->faker->,';
            }

            if ($index + 1 != count($columns))
            {
                $factoryFields .= PHP_EOL . "\t\t\t";
            }
        }

        $contents =
'<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\\' . $factory . '>
 */

class ' . $factory . ' extends Factory
{
    /**
     * Define the model\'s default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            ' . $factoryFields . '
        ];
    }
}
';

        try
        {
            $this->info(writeToFile($file, $factoriesDir, $contents, 'Factory'));
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
