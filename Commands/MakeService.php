<?php

namespace Twoscore23\LaravelBetterMakes\Commands;

use Exception;
use Illuminate\Console\Command;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name} {--blank}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Service file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = $this->argument('name');
        $isBlank = $this->option('blank');
        $model = explode('Service', $service)[0];
        $modelVar = '$' . strtolower($model);
        
        if ($service === '' || is_null($service) || empty($service)) {
            return $this->error('Service Name Invalid');
        }

$basicFunctions = '
    public function create(array $data)
    {
        try
        {
            DB::beginTransaction();

            ' . $modelVar . ' = new ' . $model . '($data);
            ' . $modelVar . '->save();

            DB::commit();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $update)
    {
        try
        {
            DB::beginTransaction();

            ' . $modelVar . ' = ' . $model . '::find($id);
            ' . $modelVar . '->fill($update);
            ' . $modelVar . '->save();

            DB::commit();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            throw $e;
        }

        return ' . $modelVar . ';
    }
    
    public function get(int $id)
    {
        ' . $modelVar . ' = ' . $model . '::find($id);

        return ' . $modelVar . ';
    }
';
$innerClass = $isBlank
    ? ''
    : $basicFunctions;
$contents=
'<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\\' . $model . '; 
        
class ' . $service . '
{' . $innerClass . '}';

        $servicesDir = app_path() . "/Services";
        $file = $servicesDir . "/$service.php";

        try
        {
            $this->info(writeToFile($file, $servicesDir, $contents, 'Service'));
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
