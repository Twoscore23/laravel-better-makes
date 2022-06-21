<?php

namespace Twoscore23\LaravelBetterMakes\Commands;

use Exception;
use Illuminate\Console\Command;

class CustomControllerMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom-controller {name} {columns} {--blank}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a controller that works with make:service';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = $this->argument('name');
        $columns = explode(',', $this->argument('columns'));
        $isBlank = (bool) $this->option('blank');
        $model = explode('Controller', $controller)[0];
        $modelVar = '$' . lcfirst($model);
        $modelSnake = formatStartCaseToSnakeCase($model);
        $modelSpaced = str_replace($modelSnake, '_', ' ');
        $modelPlural = getPlural($modelSnake);
        $service = $model . 'Service';
        $serviceVar = '$' . lcfirst($model) . 'Service';
        $serviceInit = $service . ' ' . $serviceVar;
        $accessService = $modelVar . ' = ' . $serviceVar;
        $response = '["' . $modelSnake . '" => new ' . $model . 'Resource(' . $modelVar . ')]';

        if ($controller === '' || is_null($controller) || empty($controller)) {
            return $this->error('Controller Name Invalid');
        }

        $createValidation = '';
        $updateValidation = '';
        foreach ($columns as $index=>$col)
        {
            $createValidation .= '"' . $col . '" => ';
            $updateValidation .= '"update.' . $col . '" => ';

            $relationship = getRelationship($col);
            if ($relationship)
            {
                $createValidation .= '"required|int|exists:' . getPlural($relationship) . ',id",';
                $updateValidation .= '"nullable|int|exists:' . getPlural($relationship) . ',id",';
            } else 
            {
                $createValidation .= '"",';
                $updateValidation .= '"",';
            }

            if ($index + 1 != count($columns))
            {
                $createValidation .= PHP_EOL . "\t\t\t";
                $updateValidation .= PHP_EOL . "\t\t\t";
            }
        }

        $basicFunctions = '
    public function store(Request $request, ' . $serviceInit . ')
    {
        $data = $request->validate([
            ' . $createValidation . '
        ]);
        
        try
        {
            ' . $accessService . '->create($data);
        }
        catch (Exception $e)
        {
            return response()->error("Something went wrong while trying to create ' . $modelSpaced . '.");
        }

        return response()->success(' . $response . ');
    }

    public function update(Request $request, ' . $serviceInit . ')
    {
        $update = $request->validate([
            "id" => "required|int|exists:' . $modelPlural . ',id",
            "update" => "required|array",
            ' . $updateValidation . '
        ])["update"];

        try
        {
            ' . $accessService . '->update($request->id, $update);
        }
        catch (Exception $e)
        {
            return response()->error("Something went wrong while trying to update ' . $modelSpaced . '.");
        }

        return response()->success(' . $response . ');
    }
    
    public function get(Request $request, ' . $serviceInit . ', int $id)
    {
        $request->merge(["id" => $id]);
        $request->validate([
            "id" => "required|int|exists:' . $modelPlural . ',id"
        ]);

        try
        {
            ' . $accessService . '->get($id);
        }
        catch (Exception $e)
        {
            return response()->error("Something went wrong while trying to get ' . $modelSpaced . '.");
        }

        return response()->success(' . $response . ');
    }
';
$innerClass = $isBlank
    ? ''
    : $basicFunctions;
$contents=
'<?php

namespace App\Http\Controllers;

use App\Services\\' . $service . '; 
use App\Http\Resources\\' . $model . 'Resource;
use Exception;
use Illuminate\Http\Request;
        
class ' . $controller . ' extends Controller
{' . $innerClass . '}';

        $controllersDir = app_path() . "/Http/Controllers";
        $file = $controllersDir . "/$controller.php";

        try
        {
            $this->info(writeToFile($file, $controllersDir, $contents, 'Controller'));
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
