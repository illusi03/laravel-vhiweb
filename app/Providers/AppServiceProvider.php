<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;

class AppServiceProvider extends ServiceProvider
{
    private function searchableEloquent()
    {
        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);
                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                // $query->where($relationAttribute, 'ILIKE', "%{$searchTerm}%");
                                $searchTerm = strtolower($searchTerm);
                                $query->where(DB::raw("lower($relationAttribute)"), "like", "%" . $searchTerm . "%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            // $query->orWhere($attribute, 'ILIKE', "%{$searchTerm}%");
                            $searchTerm = strtolower($searchTerm);
                            $query->orWhere(DB::raw("lower($attribute)"), "like", "%" . $searchTerm . "%");
                        }
                    );
                }
            });
            return $this;
        });
    }

    private function setDefaultTimezoneCarbon()
    {
        $configTimezone = config('app.timezone');
        date_default_timezone_set($configTimezone);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->searchableEloquent();
        $this->setDefaultTimezoneCarbon();

        // TODOS : (For Hosting)
        $this->commands([
            InstallCommand::class,
            ClientCommand::class,
            KeysCommand::class,
        ]);
    }
}
