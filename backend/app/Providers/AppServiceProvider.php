<?php

namespace App\Providers;

use App\Jobs\UpdateRentalStatusJob;
use App\Models\Rental;
use App\Models\RentalReturn;
use App\Observers\RentalObserver;
use App\Observers\RentalReturnObserver;

use App\Repositories\Eloquent\AuthorRepositoryEloquent;
use App\Repositories\Eloquent\BookImageRepositoryEloquent;
use App\Repositories\Eloquent\BookRepositoryEloquent;
use App\Repositories\Eloquent\BookReturnRepositoryEloquent;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use App\Repositories\Eloquent\DiscountRepositoryEloquent;
use App\Repositories\Eloquent\RentalDetailRepositoryEloquent;
use App\Repositories\Eloquent\RentalRepositoryEloquent;
use App\Repositories\Eloquent\UserRepositoryEloquent;
use App\Repositories\Eloquent\MonthlyTopBookRepositoryEloquent;
use App\Repositories\Eloquent\TopBookWeekRepositoryEloquent;
use App\Repositories\Eloquent\ActivityLogRepository;

use App\Repositories\Interfaces\AuthorRepository;
use App\Repositories\Interfaces\BookImageRepository;
use App\Repositories\Interfaces\BookRepository;
use App\Repositories\Interfaces\BookReturnRepository;
use App\Repositories\Interfaces\CategoryRepository;
use App\Repositories\Interfaces\DiscountRepository;
use App\Repositories\Interfaces\RentalDetailRepository;
use App\Repositories\Interfaces\RentalRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Interfaces\MonthlyTopBookRepositoryInterface;
use App\Repositories\Interfaces\TopBookWeekRepositoryInterface;
use App\Repositories\Interfaces\ActivityLogRepositoryInterface;

use App\Services\ReturnBook\LateFeeService;
use App\Services\AuthorService;
use App\Services\BookService;
use App\Services\CategoryService;
use App\Services\DiscountService;
use App\Services\MonthlyTopBookService;
use Illuminate\Support\ServiceProvider;

use App\Models\Book;
use App\Observers\ModelChangeObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind BookRepository & BookService
        $this->app->bind(BookRepository::class, BookRepositoryEloquent::class);
        $this->app->bind(BookImageRepository::class, BookImageRepositoryEloquent::class);
        $this->app->singleton(BookService::class);

        // Bind UserRepository
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService($app->make(UserRepository::class));
        });

        // Bind AuthorRepository & AuthorService
        $this->app->bind(AuthorRepository::class, AuthorRepositoryEloquent::class);
        $this->app->singleton(AuthorService::class, function ($app) {
            return new AuthorService($app->make(AuthorRepository::class));
        });

        // Bind CategoryRepository & CategoryService
        $this->app->bind(CategoryRepository::class, CategoryRepositoryEloquent::class);
        $this->app->singleton(CategoryService::class, function ($app) {
            return new CategoryService($app->make(CategoryRepository::class));
        });

        // Bind DiscountRepository & CategoryService
        $this->app->bind(DiscountRepository::class, DiscountRepositoryEloquent::class);
        $this->app->singleton(DiscountService::class, function ($app) {
            return new DiscountService($app->make(DiscountRepository::class));
        });


        $this->app->bind(RentalDetailRepository::class, RentalDetailRepositoryEloquent::class);
        $this->app->bind(RentalRepository::class, RentalRepositoryEloquent::class);
        $this->app->bind(BookReturnRepository::class, BookReturnRepositoryEloquent::class);

        $this->app->bind(UpdateRentalStatusJob::class, function ($app) {
            return new UpdateRentalStatusJob();
        });

        
        $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);

        // Monthly Top Book
        $this->app->bind(MonthlyTopBookRepositoryInterface::class, MonthlyTopBookRepositoryEloquent::class);
        $this->app->singleton(MonthlyTopBookService::class, function ($app) {
            return new MonthlyTopBookService(
                $app->make(MonthlyTopBookRepositoryInterface::class)
            );
        });
        // Weekly Top Book
        $this->app->bind(TopBookWeekRepositoryInterface::class, TopBookWeekRepositoryEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Rental::observe(RentalObserver::class);
        RentalReturn::observe(RentalReturnObserver::class);
        Book::observe(ModelChangeObserver::class);
    }
}
