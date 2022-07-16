<?php

namespace App\Providers\Repositories;

use App\Repositories\FileRepository;
use App\Repositories\Interfaces\FileRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class FileRepositoryProvider extends ServiceProvider
{
    /**
     * Register any application serivce.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            FileRepositoryInterface::class,
            FileRepository::class
        );
    }

    /**
     * Bootstrap any application service.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
