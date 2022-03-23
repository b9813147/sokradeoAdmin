<?php

namespace App\Providers;

use App\Libraries\Lang\Lang;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 設定全站語系
        app()->setLocale(Redis::get('local'));


        Relation::morphMap([
            'Tba'   => \App\Models\Tba::class,
            'Video' => \App\Models\Video::class,
        ]);

        // for  Eloquent\Builder
        \Illuminate\Database\Eloquent\Builder::macro('toSqlBinding', function () {
            $bindings = collect($this->getBindings())->map(function ($item) {
                if (!is_numeric($item)) {
                    return "'" . $item . "'";
                }
                return $item;
            })->toArray();
            return \Str::replaceArray('?', $bindings, $this->toSql());
        });
        // for  Query\Builder
        \Illuminate\Database\Query\Builder::macro('toSqlBinding', function () {
            $bindings = collect($this->getBindings())->map(function ($item) {
                if (!is_numeric($item)) {
                    return "'" . $item . "'";
                }
                return $item;
            })->toArray();

            return \Str::replaceArray('?', $bindings, $this->toSql());
        });
    }
}
