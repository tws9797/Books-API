<?php

namespace App\Providers;
use App\Book;
use App\Author;
use App\Publisher;
use App\Policies\BookPolicy;
use App\Policies\AuthorPolicy;
use App\Policies\PublisherPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Book::class => BookPolicy::class,
        Author::class => AuthorPolicy::class,
        Publisher::class => Publisher::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
