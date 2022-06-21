<?php

namespace Twoscore23\LaravelBetterMakes\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Console\Migrations\TableGuesser;

class CustomMigrationMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom-migration {name} {columns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Migration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $migration = $this->argument('name');
        $columns = explode(',', $this->argument('columns'));

        $migrationsDir = base_path() . "/database/migrations";
        $file = $migrationsDir . "/" . date('Y_m_d_His', now()->timestamp) . "_${migration}.php";

        $guesser = TableGuesser::guess($migration);
        $tableName = $guesser[0];
        $isCreate = $guesser[1];
        $functionBase = '("' . $tableName . '", function (Blueprint $table) {' . PHP_EOL . "\t\t\t";
        $upFunction = '';
        $downFunction = '';
        if ($isCreate)
        {
            $upFunction .= 'Schema::create' . $functionBase . '$table->id();';
            $downFunction .= 'Schema::dropIfExists("' . $tableName . '");';
        } else 
        {
            $upFunction .= 'Schema::table' . $functionBase;
            $downFunction .= $upFunction;
        }

        
        foreach ($columns as $index=>$col)
        {
            $upFunction .= PHP_EOL . "\t\t\t";

            $relationship = getRelationship($col);
            if ($relationship)
            {
                $upFunction .= '$table->foreignId("' . $col . '")->constrained();';
            } else 
            {
                $upFunction .= '$table->("' . $col . '");';
            }

            if (!$isCreate)
            {
                $downFunction .= '$table->dropColumn("' . $col . '");' . PHP_EOL;
            }
        }

        $upFunction .= PHP_EOL . "\t\t\t" . '$table->timestamps();' . PHP_EOL . "\t\t});";

        if (!$isCreate)
        {
            $downFunction .= '});';
        }

        $contents = 
'<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ' . $upFunction . '
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        ' . $downFunction . '
    }
};
';

        try
        {
            $this->info(writeToFile($file, $migrationsDir, $contents, 'Migration'));
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
