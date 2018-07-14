<?php
/** .-------------------------------------------------------------------
 * |      Site: www.hdcms.com  www.houdunren.com
 * |      Date: 2018/7/8 下午11:16
 * |    Author: 向军大叔 <2300071698@qq.com>
 * '-------------------------------------------------------------------*/
namespace Houdunwang\AutoCreate\Traits;

trait BuildVars
{
    protected $vars = [];
    protected $modulePath;

    protected function setVars()
    {
        /**
         * MODEL            模型名
         * MODULE            模块名
         * SMODEL           全部小写的模型名
         * NAMESPACE_CONTROLLER 控制器命名空间
         */
        $this->vars['MODEL']                 = $this->modelName;
        $this->vars['MODULE']                = $this->moduleName;
        $this->vars['NAMESPACE_HTTP']        = $this->getNameSpaceHttp();
        $this->vars['NAMESPACE_CONTROLLER']  = $this->vars['NAMESPACE_HTTP'].'Controllers';
        $this->vars['NAMESPACE_REQUEST']     = $this->vars['NAMESPACE_HTTP'].'Requests\\'.$this->modelName.'Request';
        $this->vars['NAMESPACE_MODEL']       = $this->model;
        $this->vars['SMODEL']                = snake_case($this->modelName);
        $this->vars['SMODULE']               = snake_case($this->moduleName);
        $this->vars['MODULE_PATH']           = 'Modules/'.$this->moduleName;
        $this->vars['MODEL_PATH']            = str_replace('\\', '/', basename($this->model)).'/';
        $this->vars['CONTROLLE_NAME']        = $this->modelName.'Controller';
        $this->vars['VIEW_NAME']             = $this->getViewName();
        $this->vars['VIEW_PATH']             = $this->getViewPath();
        $this->vars['CONTROLLER_PATH']       = str_replace('\\', '/', $this->vars['NAMESPACE_CONTROLLER']).'/';
        $this->vars['REQUEST_PATH']          = $this->vars['CONTROLLER_PATH'].'../Requests/';
        $this->vars['ROUTE_ROOT']            = $this->moduleName ? $this->vars['SMODULE'].'/'. $this->vars['SMODEL'] :  $this->vars['SMODEL'];
        $this->vars['CONTROLLE_INDEX_ROUTE'] = $this->moduleName ? '/'.$this->moduleName.'/'.$this->modelName : '/'.$this->modelName;
        /**
         * MIGRATION        模型名复数
         * SNAKE_MIGRATION    下划线复数小写
         * NAMPSPACE        到模块的命名空间
         * MODEL            模型名
         * SMODEL           全部小写的模型名
         * SMODULE            全部小写模块名
         * MODEL_TITLE        模型中文名在数据表设置
         */
        //$this->vars['MODEL']           = $model;
        //$this->vars['MODULE']          = $module;
        //$this->vars['MIGRATION']       = str_plural($model);
        //$this->vars['NAMESPACE']       = config('modules.namespace').'\\'.$module.'\\';
        //$this->vars['SMODEL']          = snake_case($model);
        //$this->vars['SMODULE']         = snake_case($module);
        //$this->vars['MODULE_PATH']     = config('modules.paths.modules').'/'.$module.'/';
        //$this->vars['MODEL_PATH']      = $this->vars['MODULE_PATH'].'Entities/';
        //$this->vars['CONTROLLER_PATH'] = $this->vars['MODULE_PATH'].'Http/Controllers/';
        //$this->vars['REQUEST_PATH']    = $this->vars['MODULE_PATH'].'Http/Requests/';
    }

    protected function getViewName()
    {
        if ($this->moduleName) {
            return $this->vars['SMODULE'].'::'.$this->vars['SMODEL'];
        }

        return $this->vars['SMODEL'];
    }

    protected function getViewPath()
    {
        if ($this->moduleName) {
            return 'Modules/'.$this->moduleName.'/Resources/views/'.$this->vars['SMODEL'].'/';
        }

        return 'resources/views/'.$this->vars['SMODEL'].'/';
    }

    protected function getNameSpaceHttp()
    {
        if ($this->moduleName) {
            return config('modules.namespace').'\\'.$this->moduleName.'\Http\\';
        }

        return 'App\Http\\';
    }

    protected function setVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    protected function getVar($name)
    {
        return $this->vars[$name];
    }

    protected function getAllVars()
    {
        return $this->vars;
    }

    protected function replaceVars($file)
    {
        $content = file_get_contents($file);
        foreach ($this->vars as $var => $value) {
            $content = str_replace('{'.$var.'}', $value, $content);
        }

        return $content;
    }
}
