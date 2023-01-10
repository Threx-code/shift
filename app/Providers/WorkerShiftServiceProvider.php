<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\WorkerShiftInterface;
use App\Repositories\WorkerShiftRepository;

class WorkerShiftServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(WorkerShiftInterface::class, WorkerShiftRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
