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
    protected $signature = 'hd:autocreate {model} {title}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create controller view request';
    protected $model;
    protected $modelInstance;
    protected $modelFile;
    protected $title;
    protected $modelName;
    protected $moduleName;

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
        $this->modelFile = $this->argument('model');
        if ( ! is_file($this->modelFile)) {
            $this->error("model file {$this->modelFile} no exists");

            return;
        }
        $this->title = $this->argument('title');
        $this->setVar('MODEL_TITLE', $this->title);
        $this->modelName = str_replace('.php', '', basename($this->modelFile));
        $this->model     = str_replace('.php', '', str_replace('/', '\\', $this->modelFile));
        $this->setModuleName();
        $this->setModelInstance();
        $this->setVars();
        $this->setModelFillable();
        $this->createController();
        $this->createRequest();
        $this->createRoute();
        $this->createViews();

        return;
        $this->setModuleMenus();
    }

    protected function setModuleName()
    {
        $path = str_replace('\\', '/', $this->argument('model'));
        if (preg_match('@^Modules/(.+?)/@i', $path, $match)) {
            $this->moduleName = ucfirst($match[1]);
        }
    }

    protected function setModelInstance()
    {
        $class               = ucfirst($this->model);
        $this->modelInstance = new $class;
    }

    protected function setModuleMenus()
    {
        $file  = $this->getVar('MODULE_PATH').'config/menus.php';
        $menus = include $file;
        if ( ! isset($menus[$this->getVar('SMODULE')])) {
            $menus[$this->getVar('SMODULE')] = [
                "title"      => "{$this->moduleTitle}管理",
                "icon"       => "fa fa-navicon",
                'permission' => '权限标识',
                "menus"      => [],
            ];
        }
        $menus[$this->getVar('SMODULE')]['menus'][] =
            [
                "title"      => "{$this->modelTitle}管理",
                "permission" => '',
                "url"        => "/{$this->vars['SMODULE']}/{$this->vars['SMODEL']}",
            ];
        file_put_contents($file, '<?php return '.var_export($menus, true).';');
    }

    protected function setModelFillable()
    {
        $columns = array_keys($this->getColumnData($this->modelInstance));
        $columns = implode("','", $columns);
        $content = file_get_contents($this->modelFile);
        $regp    = '@(protected\s+\$fillable\s*\=\s*\[)\s*\];@im';
        if (preg_match($regp, $content)) {
            $content = preg_replace($regp, '${1}'."'".$columns.'\'];', $content);
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
        if ($this->moduleName) {
            $file = $this->getVar('MODULE_PATH').'/Http/routes.php';
        } else {
            $file = 'routes/web.php';
        }
        $route = file_get_contents($file);
        //检测路由
        if (strstr($route, "{$this->vars['SMODEL']}-route")) {
            return;
        }
        if ($this->moduleName) {
            $route .= <<<str
\n 
//{$this->vars['SMODEL']}-route
Route::group(['middleware' => ['web', 'auth:admin'],'prefix'=>'{$this->vars['SMODULE']}','namespace'=>"Modules\\{$this->vars['MODULE']}\Http\Controllers"], 
function () {
    Route::resource('{$this->vars['SMODEL']}', '{$this->vars['MODEL']}Controller')->middleware("permission:admin,resource");
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
        $file = $this->getVar('CONTROLLER_PATH').$this->modelName.'Controller.php';

        if (is_file($file)) {
            return false;
        }
        $content = $this->replaceVars(__DIR__.'/../Build/controller.tpl');
        file_put_contents($file, $content);
        $this->info('controller create successflly');
    }

    public function createRequest()
    {
        $file = $this->getVar('REQUEST_PATH').$this->modelName.'Request.php';
        if (is_file($file)) {
            return false;
        }
        $content = $this->replaceVars(__DIR__.'/../Build/request.tpl');
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
        $rules   = [];
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
        $rules   = [];
        foreach ($columns as $column) {
            $check = $column && in_array($column['name'], $this->modelInstance->getFillAble());
            if ($check && $column['nonull']) {
                $rules[$column['name'].'.required'] = $column['title'].'不能为空';
            }
        }

        return $rules;
    }
}
