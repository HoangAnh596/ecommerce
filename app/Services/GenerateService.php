<?php

namespace App\Services;

use App\Services\Interfaces\GenerateServiceInterface;
use App\Repositories\Interfaces\GenerateRepositoryInterface as GenerateRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

/**
 * Class GenerateService
 * @package App\Services
 */
class GenerateService implements GenerateServiceInterface
{
    protected $generateRepository;

    public function __construct(GenerateRepository $generateRepository)
    {
        $this->generateRepository = $generateRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = $request->integer('perpage');
        $generates = $this->generateRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'generate/index'],
            ['id', 'DESC']
        );

        return $generates;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {

            $this->makeDatabase($request);
            $this->makeController($request);
            $this->makeModel($request);
            $this->makeRepository($request);
            $this->makeService($request);
            $this->makeProvider($request);
            $this->makeRequest($request);
            $this->makeView($request);
            if($request->input('module_type') == 1) {
                $this->makeRule($request);
            }
            $this->makeRoute($request);
            // $makeLang = $this->makeLang($request);

            $payload = $request->except('_token', 'send');
            $payload['user_id'] = Auth::id();
            $this->generateRepository->create($payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $payload = $request->except('_token', 'send');
            $this->generateRepository->update($id, $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->generateRepository->delete($id);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function makeDatabase($request) {
        DB::beginTransaction();
        try {
            $payload = $request->only('name', 'schema', 'module_type');
            $tableName = $this->convertModuleToTableName($payload['name']) . 's';
            $migrationFileName = date('Y_m_d_His') . '_' . 'create_' . $tableName . '_table.php';
            $migrationPath = database_path('migrations/' . $migrationFileName);
            $migrationTable = $this->createMigrationTable($payload);
            FILE::put($migrationPath, $migrationTable);
            if ($payload['module_type'] !== 3) {
                $foreignKey = $this->convertModuleToTableName($payload['name']) . '_id';
                $pivotTableName = $this->convertModuleToTableName($payload['name']) . '_language';
                $pivotSchema = $this->pivotSchema($tableName, $foreignKey, $pivotTableName);
                $migrationPivotTemplate = $this->createMigrationTable([
                    'schema' => $pivotSchema,
                    'name' => $pivotTableName
                ]);
                $migrationPivotFileName = date('Y_m_d_His', time() + 10) . '_' . 'create_' . $pivotTableName . '_table.php';
                $migrationPivotPath = database_path('migrations/' . $migrationPivotFileName);
                FILE::put($migrationPivotPath, $migrationPivotTemplate);
            }

            ARTISAN::call('migrate');
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function pivotSchema($tableName = '', $foreignKey = '', $pivot = '') {
        $pivotSchema = <<<SCHEMA
        Schema::create('{$pivot}', function (Blueprint \$table) {
                    \$table->unsignedBigInteger('{$foreignKey}');
                    \$table->unsignedBigInteger('language_id');
                    \$table->foreign('{$foreignKey}')->references('id')->on('{$tableName}')->onDelete('cascade');
                    \$table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
                    \$table->string('name');
                    \$table->string('canonical')->unique();
                    \$table->text('description')->nullable();
                    \$table->longText('content')->nullable();
                    \$table->string('meta_title')->nullable();
                    \$table->string('meta_keyword')->nullable();
                    \$table->text('meta_description')->nullable();
                    \$table->timestamps();
                });
        SCHEMA;
        return $pivotSchema;
    }

    private function createMigrationTable($payload) {
        // Tìm hiểu thêm về PHP Heredoc để thêm schema vào migration
        $migrationTemplate = <<<MIGRATION
        <?php
        
        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;
        
        return new class extends Migration
        {
            /**
             * Run the migrations.
             */
            public function up(): void
            {
                {$payload['schema']}
            }
        
            /**
             * Reverse the migrations.
             */
            public function down(): void
            {
                Schema::dropIfExists('{$this->convertModuleToTableName($payload['name'])}');
            }
        };
        MIGRATION;
        return $migrationTemplate;
    }

    private function makeController($request){
        $payload = $request->only('name', 'module_type');

        switch ($payload['module_type']) {
            case 1:
                $this->createTemplateController($payload['name'], 'TemplateCatalogueController');
                break;
            case 2:
                $this->createTemplateController($payload['name'], 'TemplateController');
                break;
            default:
                // $this->createSingleController();
        }
    }

    private function createTemplateController($name, $controllerFile) {
        try {
            $controllerName = $name . 'Controller.php';
            $templateControllerPath = base_path('app/Templates/' . $controllerFile . '.php');
            $controllerContent = file_get_contents($templateControllerPath);
            $replace = [
                'ModuleTemplate' => $name,
                'moduleTemplate' => lcfirst($name),
                'foreignKey' => $this->convertModuleToTableName($name) . '_id',
                'tableName' => $this->convertModuleToTableName($name) . 's',
                'moduleView' => str_replace('_', '.', $this->convertModuleToTableName($name))
            ];

            foreach ($replace as $key => $val) {
                $controllerContent = str_replace('{' . $key . '}', $replace[$key], $controllerContent);
            }

            $controllerPath = base_path('app/Http/Controllers/Backend/' . $controllerName);
            FILE::put($controllerPath, $controllerContent);

            return true;
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function makeModel($request) {
        try {
            if ($request->input('module_type') == 1) {
                $this->createModelTemplate($request);
            } else {
                dd(1112);
            }
            return true;
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function createModelTemplate($request) {
        $modelName = $request->input('name') . '.php';
        $templateModelPath = base_path('app/Templates/TemplateCatalogue.php');
        $modelContent = file_get_contents($templateModelPath);
        $module = $this->convertModuleToTableName($request->input('name'));
        $extractModule = explode('_', $module);
        $replace = [
            'ModuleTemplate' => $request->input('name'),
            'foreignKey' => $module . '_id',
            'tableName' => $module . 's',
            'relation' => $extractModule[0],
            'pivotModel' => $request->input('name') . 'Language',
            'relationPivot' => $module . '_' . $extractModule[0],
            'pivotTable' => $module . '_language',
            'module' => $module,
            'relationModel' => ucfirst($extractModule[0])
        ];
        foreach ($replace as $key => $val) {
            $modelContent = str_replace('{' . $key . '}', $replace[$key], $modelContent);
        }

        $modelPath = base_path('app/Models/' . $modelName);
        FILE::put($modelPath, $modelContent);
    }

    private function makeRepository($request) {
        try {
            $name = $request->input('name');
            $module = $this->convertModuleToTableName($name);
            $moduleExtract = explode('_', $module);
            $repository = $this->initializeServiceLayer('Repository', 'Repositories', $request);
            $replace = [
                'Module' => $name,
            ];
            $repositoryInterfaceContent = $repository['interface']['layerInterfaceContent'];
            $repositoryInterfacePath = $repository['interface']['layerInterfacePath'];
            $repositoryInterfaceContent = str_replace('{Module}', $replace['Module'], $repositoryInterfaceContent);

            $replaceRepository = [
                'Module' => $name,
                'tableName' => $module . 's',
                'pivotTableName' => $module . '_' . $moduleExtract[0],
                'foreignKey' => $module . '_id',
            ];
            $repositoryContent = $repository['service']['layerContent'];
            $repositoryPath = $repository['service']['layerPathPut'];
            foreach ($replaceRepository as $key => $val) {
                $repositoryContent = str_replace('{' . $key . '}', $replaceRepository[$key], $repositoryContent);
            }
            
            FILE::put($repositoryInterfacePath, $repositoryInterfaceContent);
            FILE::put($repositoryPath, $repositoryContent);

            return true;
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function makeService($request) {
        try {
            $name = $request->input('name');
            $module = $this->convertModuleToTableName($name);
            $moduleExtract = explode('_', $module);
            $service = $this->initializeServiceLayer('Service', 'Services', $request);

            $replace = [
                'Module' => $name,
            ];
            $serviceInterfaceContent = $service['interface']['layerInterfaceContent'];
            $serviceInterfacePath = $service['interface']['layerInterfacePath'];
            $serviceInterfaceContent = str_replace('{Module}', $replace['Module'], $serviceInterfaceContent);

            $replaceService = [
                'Module' => $name,
                'module' => lcfirst($name),
                'tableName' => $module . 's',
                'foreignKey' => $module . '_id',
                'name' => $moduleExtract[0]
            ];
            $serviceContent = $service['service']['layerContent'];
            $servicePath = $service['service']['layerPathPut'];
            foreach ($replaceService as $key => $val) {
                $serviceContent = str_replace('{' . $key . '}', $replaceService[$key], $serviceContent);
            }
            
            FILE::put($serviceInterfacePath, $serviceInterfaceContent);
            FILE::put($servicePath, $serviceContent);

            return true;
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function initializeServiceLayer($layer = '', $folder = '', $request) {
        $name = $request->input('name');
        $option = [
            $layer.'Name' => $name.$layer,
            $layer.'InterfaceName' => $name.$layer.'Interface'
        ];

        $layerInterfaceRead = base_path('app/Templates/Template'.$layer.'Interface.php');
        $layerInterfaceContent = file_get_contents($layerInterfaceRead);
        $layerInterfacePath = base_path('app/'.$folder.'/Interfaces/' . $option[$layer.'InterfaceName'] . '.php');

        $layerPathRead = base_path('app/Templates/Template'.$layer.'.php');
        $layerContent = file_get_contents($layerPathRead);
        $layerPathPut = base_path('app/'.$folder.'/' . $option[$layer.'Name'] . '.php');
        
        return [
            'interface' => [
                'layerInterfaceContent' => $layerInterfaceContent,
                'layerInterfacePath' => $layerInterfacePath
            ],
            'service' => [
                'layerContent' => $layerContent,
                'layerPathPut' => $layerPathPut
            ],
        ];
    }

    private function makeProvider($request) {
        try {
            $name = $request->input('name');
            $provider = [
                'providerPath' => base_path('app/Providers/AppServiceProvider.php'),
                'repositoryProviderPath' => base_path('app/Providers/RepositoryServiceProvider.php'),
            ];
            
            foreach($provider as $key => $val){
                $content = file_get_contents($val);
                $insertLine = ($key == 'providerPath') ? "'App\\Services\\Interfaces\\{$name}ServiceInterface' => 'App\\Services\\{$name}Service'," : "'App\\Repositories\\Interfaces\\{$name}RepositoryInterface' => 'App\\Repositories\\{$name}Repository',"; 

                $position = strpos($content, '];');

                if($position !== false){
                    $newContent = substr_replace($content, "    ".$insertLine . "\n".'    ', $position, 0);
                }
                File::put($val, $newContent);
            }

            return true;
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function makeRequest($request) {
        try {
            $name = $request->input('name');
            $requestArray = ['Store'.$name.'Request', 'Update'.$name.'Request', 'Delete'.$name.'Request'];
            $requestTemplate = ['TemplateRequestStore', 'TemplateRequestUpdate', 'TemplateRequestDelete'];
            if($request->input('module_type') != 1) {
                unset($requestArray[2]);
                unset($requestTemplate[2]);
            }
            
            foreach($requestTemplate as $key => $val) {
                $requestPath = base_path('app/Templates/'.$val.'.php');
                $requestContent = file_get_contents($requestPath);
                $requestContent = str_replace('{Module}', $name, $requestContent);
                $requestPut = base_path('app/Http/Requests/'.$requestArray[$key].'.php');

                File::put($requestPut, $requestContent);
            }

            return true;
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function makeView($request) {
        try {
            $name = $request->input('name');
            $module = $this->convertModuleToTableName($name);
            $extractModule = explode('_', $module);
            $basePath = resource_path("views/backend/{$extractModule[0]}");
            $folderPath = (count($extractModule) == 2) ? "$basePath/{$extractModule[1]}" : "$basePath/{$extractModule[0]}";
            $componentPath = "$folderPath/component";

            $this->createDirectory(($folderPath));
            $this->createDirectory(($componentPath));
            $viewPath = (count($extractModule) == 2) ? "{$extractModule[0]}.{$extractModule[1]}" : "{$extractModule[0]}";
            $replacement = [
                'view' => $viewPath,
                'module' => lcfirst($name),
                'Module' => $name
            ];

            $sourcePath = base_path('app/Templates/views/'.((count($extractModule) == 2) ? 'catalogue' : 'post').'/');
            $fileArray = ['store.blade.php', 'index.blade.php', 'delete.blade.php'];
            $componentFile = ['aside.blade.php', 'filter.blade.php', 'table.blade.php'];
            $this->CopyAndReplaceContent($sourcePath, $folderPath, $fileArray, $replacement);
            $this->CopyAndReplaceContent("{$sourcePath}component/", $componentPath, $componentFile, $replacement);

            return true;
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function createDirectory($path) {
        if(!FILE::exists($path)) {
            FILE::makeDirectory($path, 0755, true);
        }
    }

    private function CopyAndReplaceContent(string $sourcePath, string $destinationPath, 
        array $fileArray, array $replacement
    ){
        foreach($fileArray as $key => $val) {
            $sourceFile = $sourcePath.$val;
            $destination = "{$destinationPath}/{$val}";
            $content = file_get_contents($sourceFile);
            
            foreach($replacement as $keyReplace => $replace){
                $content = str_replace('{'.$keyReplace.'}', $replace, $content);
            }
            if(!FILE::exists($destination)) {
                FILE::put($destination, $content);
            }
        }
    }

    private function makeRule($request){
        $name = $request->input('name');
        $destination = base_path('app/Rules/Check'.$name.'ChildrenRule.php');
        $ruleTemplate = base_path('app/Templates/RuleTemplate.php');
        $content = file_get_contents($ruleTemplate);
        $content = str_replace('{Module}', $name, $content);
        if(!FILE::exists($destination)){
            FILE::put($destination, $content);
        }
    }

    private function makeRoute($request) {
        try {
            $name = $request->input('name');
            $module = $this->convertModuleToTableName($name);
            $moduleExtract = explode('_', $module);
            $routesPath = base_path('routes/web.php');
            $content = file_get_contents($routesPath);
            $routeUrl = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}/$moduleExtract[1]" : $moduleExtract[1];
            $routeName = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}.$moduleExtract[1]" : $moduleExtract[0];
            
            $routeGroup = <<<ROUTE
            /* {$name} */
                Route::group(['prefix' => '{$routeUrl}'], function (){
                    Route::get('index', [{$name}Controller::class, 'index'])->name('{$routeName}.index');
                    Route::get('create', [{$name}Controller::class, 'create'])->name('{$routeName}.create');
                    Route::post('store', [{$name}Controller::class, 'store'])->name('{$routeName}.store');
                    Route::get('{id}/edit', [{$name}Controller::class, 'edit'])->where(['id' => '[0-9]+'])->name('{$routeName}.edit');
                    Route::post('{id}/update', [{$name}Controller::class, 'update'])->where(['id' => '[0-9]+'])->name('{$routeName}.update');
                    Route::get('{id}/delete', [{$name}Controller::class, 'delete'])->where(['id' => '[0-9]+'])->name('{$routeName}.delete');
                    Route::delete('{id}/destroy', [{$name}Controller::class, 'destroy'])->where(['id' => '[0-9]+'])->name('{$routeName}.destroy');
                });

                //@@new-module@@
            ROUTE;

            $useController = <<<ROUTE
            use App\Http\Controllers\Backend\\{$name}Controller;
            //@@useController@@
            ROUTE;
            $content = str_replace('//@@new-module@@', $routeGroup, $content);
            $content = str_replace('//@@useController@@', $useController, $content);
            FILE::put($routesPath, $content);

            Artisan::call('route:cache');
            return true;
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function convertModuleToTableName($name)
    {
        $temp = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

        return $temp;
    }

    private function paginateSelect()
    {
        return ['id', 'name', 'schema'];
    }
}
