<?php
/** .-------------------------------------------------------------------
 * |      Site: www.hdcms.com  www.houdunren.com
 * |      Date: 2018/7/8 下午11:16
 * |    Author: 向军大叔 <2300071698@qq.com>
 * '-------------------------------------------------------------------*/
namespace Houdunwang\AutoCreate\Traits;

trait CreateView
{
    protected function createIndexBlade()
    {
        //首页
        $COLUMNS = '';
        foreach ($this->formatColumns() as $column) {
            if (isset($column['options']) && count($column['options']) >= 2) {
                $COLUMNS .= "<th>{$column['title']}</th>";
            }
        }
        $this->setVar('COLUMNS', $COLUMNS);
        $COLUMNS_VALUE = '';
        foreach ($this->formatColumns() as $column) {
            if (isset($column['options']) && count($column['options']) >= 2) {
                if ($column['options'][1] == 'image') {
                    $COLUMNS_VALUE .= "<td><img src='{!! \$d['{$column['name']}'] !!}' style='width:45px;height:45px;'/></td>";
                } else {
                    $COLUMNS_VALUE .= "<td>{!! \$d['{$column['name']}'] !!}</td>";
                }
            }
        }
        $this->setVar('COLUMNS_VALUE', $COLUMNS_VALUE);
        $content = $this->replaceVars(__DIR__."/../Build/views/index.blade.php");
        $file    = $this->vars['VIEW_PATH']."/index.blade.php";
        if ( ! is_file($file)) {
            file_put_contents($file, $content);
            $this->info('index.blade.php view create successflly');
        }
    }

    protected function createCreateAndEditBlade()
    {
        $content = '';
        foreach ($this->formatColumns() as $column) {
            if (isset($column['options']) && count($column['options']) >= 2) {
                $forms = ['checkbox', 'date', 'datetime', 'image', 'input', 'radio', 'select', 'simditor', 'textarea'];
                if (in_array($column['options'][1], $forms)) {
                    $this->setVar("column['title']", $column['title']);
                    $this->setVar("column['name']", $column['name']);
                    $action  = '_'.$column['options'][1];
                    $content .= $this->$action($column);
                }
            }
        }

        $file = $this->vars['VIEW_PATH'].'/create.blade.php';
        if ( ! is_file($file)) {
            $this->setVar('HTML', $content);
            $content = $this->replaceVars(__DIR__.'/../Build/views/create.blade.php');
            file_put_contents($this->vars['VIEW_PATH'].'/create.blade.php', $content);
            $this->info('create.blade.php view create successflly');
        }

        $file = $this->vars['VIEW_PATH'].'/edit.blade.php';
        if ( ! is_file($file)) {
            $this->info('create.blade.php view create successflly');
            $content = $this->replaceVars(__DIR__.'/../Build/views/edit.blade.php');
            file_put_contents($this->vars['VIEW_PATH'].'/edit.blade.php', $content);
            $this->info('edit.blade.php view create successflly');
        }
    }

    protected function _input($column)
    {
        $file = __DIR__.'/../Build/forms/input.blade.php';

        return $this->replaceVars($file);
    }

    protected function _textarea($column)
    {
        $file = __DIR__.'/../Build/forms/textarea.blade.php';

        return $this->replaceVars($file);
    }

    protected function _date($column)
    {
        $file = __DIR__.'/../Build/forms/date.blade.php';

        return $this->replaceVars($file);
    }

    protected function _datetime($column)
    {
        $file = __DIR__.'/../Build/forms/datetime.blade.php';

        return $this->replaceVars($file);
    }

    protected function _simditor($column)
    {
        $file = __DIR__.'/../Build/forms/simditor.blade.php';

        return $this->replaceVars($file);
    }

    protected function _image($column)
    {
        $file = __DIR__.'/../Build/forms/image.blade.php';

        return $this->replaceVars($file);
    }

    protected function _checkbox($column)
    {
        $content = '';
        if (isset($column['options']) && count($column['options']) >= 3) {
            foreach ($column['options'][2] as $value => $title) {
                $content .= <<<str
            <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="{$column['name']}-{$value}"
                           {{ old('{$column['name']}',\${$this->vars['SMODEL']}['{$column['name']}'])=='{$value}'?'checked':''}}
                           name="{$column['name']}" value="{$value}">
                    <label class="form-check-label" for="{$column['name']}-{$value}">{$title}</label>
           </div>
str;
            }
        }
        if ($content) {
            $this->setVar('FORM_HTML', $content);
            $file = __DIR__.'/../Build/forms/checkbox.blade.php';

            return $this->replaceVars($file);
        }
    }

    protected function _select($column)
    {
        $content = '';
        if (isset($column['options']) && count($column['options']) >= 3) {
            foreach ($column['options'][2] as $value => $title) {
                $content .= <<<str
           <option value="{$value}"
            {{old('{$column['name']}',\${$this->vars['SMODEL']}['{$column['name']}'])=='{$value}'?'selected':''}} 
            >{$title}</option>
str;
            }
        }
        if ($content) {
            $this->setVar('FORM_HTML', $content);
            $file = __DIR__.'/../Build/forms/select.blade.php';

            return $this->replaceVars($file);
        }
    }

    protected function _radio($column)
    {
        $content = '';
        if (isset($column['options']) && count($column['options']) >= 3) {
            foreach ($column['options'][2] as $value => $title) {
                $content .= <<<str
            <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                           {{old('{$column['name']}',\${$this->vars['SMODEL']}['{$column['name']}'])=='{$value}'?'checked':''}}
                           name="{$column['name']}" value="{$value}"
                           id="{$column['name']}-{$value}">
                    <label class="form-check-label" for="{$column['name']}-{$value}">{$title}</label>
           </div>
str;
            }
        }
        if ($content) {
            $this->setVar('FORM_HTML', $content);
            $file = __DIR__.'/../Build/forms/radio.blade.php';

            return $this->replaceVars($file);
        }
    }
}













