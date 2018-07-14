<?php
/** .-------------------------------------------------------------------
 * |      Site: www.hdcms.com
 * |      Date: 2018/6/25 下午2:54
 * |    Author: 向军大叔 <2300071698@qq.com>
 * '-------------------------------------------------------------------*/

namespace Houdunwang\AutoCreate;

use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    public $singletons = [
        'hd-autocreate' => MenusService::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ModuleCreateCommand::class,
                ConfigCreateCommand::class,
                PermissionCreateCommand::class,
                ModelCreateCommand::class,
                BuildCreateCommand::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        //配置文件
        $this->publishes([
            __DIR__.'/hd_module.php' => config_path('hd_module.php'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('HDModule', function () {
            return new Provider();
        });
    }
}
