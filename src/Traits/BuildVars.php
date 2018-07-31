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
        $this->vars['MODEL']                 = $this->model;
        $this->vars['MODULE']                = $this->module;
        $this->vars['NAMESPACE_HTTP']        = $this->getNameSpaceHttp();
        $this->vars['NAMESPACE_CONTROLLER']  = $this->vars['NAMESPACE_HTTP'].'Controllers';
        $this->vars['NAMESPACE_REQUEST']     = $this->vars['NAMESPACE_HTTP'].'Requests\\'.$this->model.'Request';
        $this->vars['NAMESPACE_MODEL']       = $this->modelClass;
        $this->vars['SMODEL']                = snake_case($this->model);
        $this->vars['SMODULE']               = snake_case($this->module);
        $this->vars['MODULE_PATH']           = config('modules.paths.modules'). '/'.$this->module.'/';
        $this->vars['MODEL_PATH']            = $this->vars['MODULE_PATH']. '/'.config('modules.paths.generator.model.path');
        $this->vars['CONTROLLE_NAME']        = $this->model.'Controller';
        $this->vars['VIEW_NAME']             = $this->getViewName();
        $this->vars['VIEW_PATH']             = $this->getViewPath();
        $this->vars['CONTROLLER_PATH']       = $this->vars['MODULE_PATH'].'/Http/Controllers/';
        $this->vars['REQUEST_PATH']          = $this->vars['CONTROLLER_PATH'].'../Requests/';
        $this->vars['ROUTE_ROOT']            = $this->module ? $this->vars['SMODULE'].'/'. $this->vars['SMODEL'] :  $this->vars['SMODEL'];
        $this->vars['CONTROLLE_INDEX_ROUTE'] = $this->module ? '/'.$this->module.'/'.$this->model : '/'.$this->model;
    }

    protected function getViewName()
    {
        if ($this->module) {
            return $this->vars['SMODULE'].'::'.$this->vars['SMODEL'];
        }

        return $this->vars['SMODEL'];
    }

    protected function getViewPath()
    {
        if ($this->module) {
            return $this->vars['MODULE_PATH'].'/Resources/views/'.$this->vars['SMODEL'].'/';
        }

        return 'resources/views/'.$this->vars['SMODEL'].'/';
    }

    protected function getNameSpaceHttp()
    {
        if ($this->module) {
            return config('modules.namespace').'\\'.$this->module.'\Http\\';
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
