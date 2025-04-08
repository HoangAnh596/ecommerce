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
            if($request->input('module_type') == 'catalogue') {
                $this->makeRule($request);
            }
            $this->makeRoute($request);
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
        $payload = $request->only('name', 'schema', 'module_type');
        $module = $this->convertModuleToTableName($payload['name']);
        $moduleExtract = explode('_', $module);
        $this->makeMainTable($payload, $module);
        if ($request->input('module_type') !== 'difference') {
            $this->makeLanguageTable($module);
            if(count($moduleExtract) == 1){
                $this->makeRelationTable($module);
            }
        }

        ARTISAN::call('migrate');
    }

    private function makeRelationTable($module){
        $moduleExtract = explode('_', $module);
        $tableName = $module.'_catalogue_'.$moduleExtract[0];
        $schema = $this->relationSchema($tableName, $module);
        $migarationRelationFile = $this->createMigrationTable($schema, $tableName);
        $migrationRelationFileName = date('Y_m_d_His', time() + 10) . '_create_' . $tableName . '_table.php';
        $migrationRelationPath = database_path('migrations/' . $migrationRelationFileName);

        FILE::put($migrationRelationPath, $migarationRelationFile);
    }

    private function makeLanguageTable($module){
        $pivotSchema = $this->pivotSchema($module);
        $dropPivotTable = $module.'_language';
        $migrationPivotTemplate = $this->createMigrationTable($pivotSchema, $dropPivotTable);
        $migrationPivotFileName = date('Y_m_d_His', time() + 10) . '_' . 'create_' . $module . '_language_table.php';
        $migrationPivotPath = database_path('migrations/' . $migrationPivotFileName);
   
        FILE::put($migrationPivotPath, $migrationPivotTemplate);
    }

    private function makeMainTable($payload, $module){
        $tableName =  $module.'s';
        $migrationFileName = date('Y_m_d_His') . '_create_' . $tableName . '_table.php';
        $migrationPath = database_path('migrations/' . $migrationFileName);
        $migrationTable = $this->createMigrationTable($payload['schema'], $tableName);

        FILE::put($migrationPath, $migrationTable);
    }

    private function relationSchema($tableName, $module = '') {
        $schema = <<<SCHEMA
        Schema::create('{$tableName}', function (Blueprint \$table) {
                    \$table->unsignedBigInteger('{$module}_catalogue_id');
                    \$table->unsignedBigInteger('{$module}_id');
                    \$table->foreign('{$module}_catalogue_id')->references('id')->on('{$module}_catalogues')->onDelete('cascade');
                    \$table->foreign('{$module}_id')->references('id')->on('{$module}s')->onDelete('cascade');
                });
        SCHEMA;

        return $schema;
    }

    private function pivotSchema($module = '') {
        $pivotSchema = <<<SCHEMA
        Schema::create('{$module}_language', function (Blueprint \$table) {
                    \$table->unsignedBigInteger('{$module}_id');
                    \$table->unsignedBigInteger('language_id');
                    \$table->foreign('{$module}_id')->references('id')->on('{$module}s')->onDelete('cascade');
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

    private function createMigrationTable($schema, $dropTable = '') {
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
                {$schema}
            }
        
            /**
             * Reverse the migrations.
             */
            public function down(): void
            {
                Schema::dropIfExists('{$dropTable}');
            }
        };
        MIGRATION;
        return $migrationTemplate;
    }

    private function makeController($request){
        $payload = $request->only('name', 'module_type');
        switch ($payload['module_type']) {
            case 'catalogue':
                $this->createTemplateController($payload['name'], 'PostCatalogueController');
                break;
            case 'detail':
                $this->createTemplateController($payload['name'], 'PostController');
                break;
            default:
                echo 1;die();
            break;
        }
    }

    private function createTemplateController($name, $controllerFile) {
        $controllerName = $name . 'Controller.php';
        $templateControllerPath = base_path('app/Templates/controllers/' . $controllerFile . '.php');
        $module = explode('_', $this->convertModuleToTableName($name));
        $controllerContent = file_get_contents($templateControllerPath);
        $replace = [
            '$class' => ucfirst(current($module)),
            'module' => lcfirst(current($module))
        ];

        $controllerContent = $this->replaceContent($controllerContent, $replace);
        $controllerPath = base_path('app/Http/Controllers/Backend/' . $controllerName);

        FILE::put($controllerPath, $controllerContent);
    }

    private function makeModel($request) {
        try {
            $moduleType = $request->input('module_type');
            $modelName = $request->input('name') . '.php';
            switch ($moduleType) {
                case 'catalogue':
                    $this->createCatalogueModel($request, $modelName);
                    break;
                case 'detail':
                    $this->createModel($request, $modelName);
                    break;
                default:
                dd(555);
                    // $this->createSingleController();
            }

            return true;
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function createModel($request, $modelName) {
        $template = base_path('app/Templates/models/Post.php');
        $content = file_get_contents($template);
        $module = $this->convertModuleToTableName($request->input('name'));
        $replacement = [
            '$class' => ucfirst($module),
            '$module' => $module
        ];
        $content = $this->replaceContent($content, $replacement);
        $this->createModelFile($modelName, $content);
    }

    private function replaceContent($content, $replace) {
        foreach ($replace as $key => $val) {
            $content = str_replace('{' . $key . '}', $val, $content);
        }
        return $content;
    }

    private function createCatalogueModel($request, $modelName) {
        $module = $this->convertModuleToTableName($request->input('name'));
        $extractModule = explode('_', $module);

        $replacements = [
            '$class' => ucfirst($extractModule[0]),
            '$module' => $extractModule[0]
        ];
        $modelRelationName = $request->input('name') . 'Language.php';
        $templates = [
            $modelName => base_path('app/Templates/models/PostCatalogue.php'),
            $modelRelationName => base_path('app/Templates/models/PostCatalogueLanguage.php')
        ];

        foreach ($templates as $fileName => $templatePath) {
            $content = file_get_contents($templatePath);
            foreach ($replacements as $key => $value) {
                $content = str_replace('{' . $key . '}', $value, $content);
            }
            $this->createModelFile($fileName, $content);
        }

    }

    private function createModelFile($modelName, $modelContent) {
        $modelPath = base_path('app/Models/' . $modelName);
        FILE::put($modelPath, $modelContent);
    }

    private function makeRepository($request) {
        $name = $request->input('name');
        $module = explode('_', $this->convertModuleToTableName($name));
        $repositoryPath = (count($module) == 1)
            ? base_path('app/Templates/repositories/PostRepository.php') 
            : base_path('app/Templates/repositories/PostCatalogueRepository.php');
        $path = [
            'Interface' => base_path('app/Templates/repositories/TemplateRepositoryInterface.php'),
            'Repositories' => $repositoryPath
        ];
            
        $replacement = [
            '$class' => ucfirst(current($module)),
            'module' => lcfirst(current($module)),
            '$extend' => (count($module) == 2) ? 'Catalogue' : '',
        ];
        foreach($path as $key => $val) {
            $content = file_get_contents($val);
            $newContent = $this->replaceContent($content, $replacement);
            $contentPath = ($key == 'Interface') 
                ? base_path('app/Repositories/Interfaces/' . $name . 'RepositoryInterface.php') 
                : base_path('app/Repositories/' . $name . 'Repository.php');
            FILE::put($contentPath, $newContent);
        }
    }

    private function makeService($request) {
        $name = $request->input('name');
        $module = explode('_', $this->convertModuleToTableName($name));
        $servicePath = (count($module) == 1)
            ? base_path('app/Templates/services/PostService.php') 
            : base_path('app/Templates/services/PostCatalogueService.php');
        $path = [
            'Interface' => base_path('app/Templates/services/TemplateServiceInterface.php'),
            'Services' => $servicePath
        ];
            
        $replacement = [
            '$class' => ucfirst(current($module)),
            'module' => lcfirst(current($module)),
            '$extend' => (count($module) == 2) ? 'Catalogue' : '',
        ];
        foreach($path as $key => $val) {
            $content = file_get_contents($val);
            $newContent = $this->replaceContent($content, $replacement);
            $contentPath = ($key == 'Interface') 
                ? base_path('app/Services/Interfaces/' . $name . 'ServiceInterface.php') 
                : base_path('app/Services/' . $name . 'Service.php');
            FILE::put($contentPath, $newContent);
        }
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

        $name = $request->input('name');
        $resName = lcfirst($name);
        $requestArray = ['Store'.$name.'Request', 'Update'.$name.'Request', 'Delete'.$name.'Request'];
        $requestTemplate = ['TemplateRequestStore', 'TemplateRequestUpdate', 'TemplateRequestDelete'];
        if($request->input('module_type') != 'catalogue') {
            unset($requestArray[2]);
            unset($requestTemplate[2]);
        }

        foreach($requestTemplate as $key => $val) {
            $requestPath = base_path('app/Templates/requests/'.$val.'.php');
            $requestContent = file_get_contents($requestPath);
            $requestContent = str_replace('{Module}', $name, $requestContent);
            $requestContent = str_replace('{resName}', $resName, $requestContent);
            $requestPut = base_path('app/Http/Requests/'.$requestArray[$key].'.php');

            File::put($requestPut, $requestContent);
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
            $routeUrl = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}/$moduleExtract[1]" : $moduleExtract[0];
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
