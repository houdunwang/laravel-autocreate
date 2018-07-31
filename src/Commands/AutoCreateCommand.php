<?php

namespace Houdunwang\AutoCreate\Commands;

use Houdunwang\AutoCreate\Traits\CreateView;
use Houdunwang\AutoCreate\Traits\Db;
use Houdunwang\AutoCreate\Traits\BuildVars;
use Illuminate\Console\Command;
use Artisan;
use Storage;

class AutoCreateCommand extends Command
{
    use BuildVars, Db, CreateView;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hd:autocreate {model} {module} {title}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create controller view request';
    protected $model;
    protected $modelClass;
    protected $modelInstance;
    protected $modelFile;
    protected $module;
    protected $title;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->model = $this->argument('model');
        $this->module = $this->argument('module');
        $this->modelFile = config('modules.paths.modules') . '/' . $this->module . '/'
            . config('modules.paths.generator.model.path') . '/' . $this->argument('model') . '.php';
        $this->modelClass = config('modules.namespace') . '\\' . $this->module . '\\' . config('modules.paths.generator.model.path')
            . '\\' . $this->argument('model');
        if (!is_file($this->modelFile)) {
            $this->error("model file {$this->modelFile} no exists");

            return;
        }
        $this->title = $this->argument('title');
        $this->setVar('MODEL_TITLE', $this->title);
        $this->setModelInstance();
        $this->setVars();
        $this->setModelFillable();
        $this->createController();
        $this->createRequest();
        $this->createRoute();
        $this->createViews();
        $this->setModuleMenus();
    }

    protected function setModelInstance()
    {
        $this->modelInstance = new $this->modelClass;
    }

    protected function setModuleMenus()
    {
        $file = $this->getVar('MODULE_PATH') . '/Config/menus.php';
        $menus = include $file;
        if (!isset($menus[$this->getVar('SMODULE')])) {
            $menus[$this->getVar('SMODULE')] = [
                "title" => "{$this->title}管理",
                "icon" => "fa fa-navicon",
                'permission' => '权限标识',
                "menus" => [],
            ];
        }
        $menus[$this->getVar('SMODULE')]['menus'][] =
            [
                "title" => "{$this->title}管理",
                "permission" => '',
                "url" => "/{$this->vars['SMODULE']}/{$this->vars['SMODEL']}",
            ];
        file_put_contents($file, '<?php return ' . var_export($menus, true) . ';');
        $this->info('menu create successfully');
    }

    protected function setModelFillable()
    {
        $columns = array_keys($this->getColumnData());
        $columns = implode("','", $columns);
        $content = file_get_contents($this->modelFile);
        $regp = '@(protected\s+\$fillable\s*\=\s*\[)\s*\];@im';
        if (preg_match($regp, $content)) {
            $content = preg_replace($regp, '${1}' . "'" . $columns . '\'];', $content);
            file_put_contents($this->modelFile, $content);
            $this->info('update model fillable attribute');
        }
    }

    protected function createViews()
    {
        $dir = $this->vars['VIEW_PATH'];
        is_dir($dir) or mkdir($dir, 0755, true);
        $this->createIndexBlade();
        $this->createCreateAndEditBlade();
    }

    protected function createRoute()
    {
        if ($this->module) {
            $file = $this->getVar('MODULE_PATH') . '/Http/routes.php';
        } else {
            $file = 'routes/web.php';
        }
        $route = file_get_contents($file);
        //检测路由
        if (strstr($route, "{$this->vars['SMODEL']}-route")) {
            return;
        }
        if ($this->module) {
            $route .= <<<str
\n 
//{$this->vars['SMODEL']}-route
Route::group(['middleware' => ['web'],'prefix'=>'{$this->vars['SMODULE']}','namespace'=>"{$this->vars['NAMESPACE_HTTP']}\Controllers"], 
function () {
    Route::resource('{$this->vars['SMODEL']}', '{$this->vars['MODEL']}Controller');
});
str;
        } else {
            $route .= <<<str
\n 
//{$this->vars['SMODEL']}-route
Route::resource('{$this->vars['SMODEL']}', '{$this->vars['MODEL']}Controller');
str;
        }
        file_put_contents($file, $route);
        $this->info('route create successfully');
    }

    public function createController()
    {
        $file = $this->getVar('CONTROLLER_PATH') . $this->model . 'Controller.php';

        if (is_file($file)) {
            return false;
        }
        $content = $this->replaceVars(__DIR__ . '/../Build/controller.tpl');
        file_put_contents($file, $content);
        $this->info('controller create successflly');
    }

    public function createRequest()
    {
        $file = $this->getVar('REQUEST_PATH') . $this->model . 'Request.php';
        if (is_file($file)) {
            return false;
        }
        $content = $this->replaceVars(__DIR__ . '/../Build/request.tpl');
        $content = str_replace('{REQUEST_RULE}', var_export($this->getRequestRule(), true), $content);
        $content = str_replace('{REQUEST_RULE_MESSAGE}', var_export($this->getRequestRuleMessage(), true), $content);
        file_put_contents($file, $content);
        $this->info('request create successflly');
    }

    /**
     * 设置验证规则
     *
     * @return array
     */
    protected function getRequestRule()
    {
        $columns = $this->formatColumns();
        $rules = [];
        foreach ($columns as $column) {
            $check = $column && in_array($column['name'], $this->modelInstance->getFillAble());
            if ($check && $column['nonull']) {
                $rules[$column['name']] = 'required';
            }
        }

        return $rules;
    }

    /**
     * 验证提示信息
     *
     * @return array
     */
    protected function getRequestRuleMessage()
    {
        $columns = $this->formatColumns();
        $rules = [];
        foreach ($columns as $column) {
            $check = $column && in_array($column['name'], $this->modelInstance->getFillAble());
            if ($check && $column['nonull']) {
                $rules[$column['name'] . '.required'] = "请设置 " . $column['title'];
            }
        }

        return $rules;
    }
}
