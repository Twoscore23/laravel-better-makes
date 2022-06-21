<?php

namespace Twoscore23\LaravelBetterMakes\Commands;

use Exception;
use Illuminate\Console\Command;

class CustomResourceMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom-resource {name} {columns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Resource';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $resource = $this->argument('name');
        $columns = explode(',', $this->argument('columns'));

        $resourcesDir = app_path() . "/Http/Resources";
        $file = $resourcesDir . "/${resource}.php";

        $resourceFields = '"id" => $this->id,' . PHP_EOL . "\t\t\t";
        foreach ($columns as $index=>$col)
        {
            $resourceFields .= '"' . $col . '" => $this->' . $col . ',';

            $relationship = getRelationship($col);
            if ($relationship)
            {
                $relationshipStartCase = formatSnakeCaseToStartCase($relationship);
                $resourceFields .= PHP_EOL . "\t\t\t" . '"' . $relationship . '" => new ' . $relationshipStartCase . 'Resource($this->whenLoaded("' . lcfirst($relationshipStartCase) . '")),';
            }

            if ($index + 1 != count($columns))
            {
                $resourceFields .= PHP_EOL . "\t\t\t";
            }
        }

        $contents = 
'<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ' . $resource . ' extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            ' . $resourceFields . '
        ];
    }
}';

        try
        {
            $this->info(writeToFile($file, $resourcesDir, $contents, 'Resource'));
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
