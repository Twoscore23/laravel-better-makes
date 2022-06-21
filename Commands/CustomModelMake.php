<?php

namespace Twoscore23\LaravelBetterMakes\Commands;

use Exception;
use Illuminate\Console\Command;

class CustomModelMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom-model {name} {columns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Model';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = $this->argument('name');
        $columns = explode(',', $this->argument('columns'));

        $modelsDir = app_path() . '/Models';
        $file = $modelsDir . "/$model.php";

        $fillable = '';
        $relationships = '';
        foreach ($columns as $index=>$col)
        {
            $fillable .= '"' . $col . '",';

            if ($index + 1 != count($columns))
            {
                $fillable .= PHP_EOL . "\t\t";
            }

            if (str_contains($col, '_id'))
            {
                $relationshipName = formatSnakeCaseToCamelCase(substr($col, 0, strpos($col, '_id')));
                $relationships .= 
    'public function ' . $relationshipName . '()
    {
        return $this->belongsTo(' . $relationshipName . '::class);
    }' . PHP_EOL . PHP_EOL . "\t";
            }
        }

        $contents =
'<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ' . $model . ' extends Model
{
    use HasFactory;

    protected $fillable = [
        ' . $fillable . '
    ];

    // RELATIONSHIPS
    ' . $relationships . '// ATTRIBUTES
}
';

        try
        {
            $this->info(writeToFile($file, $modelsDir, $contents, 'Model'));
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
