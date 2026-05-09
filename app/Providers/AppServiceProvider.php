<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Policies\CategoryPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\SalesOrderPolicy;
use App\Policies\StockMovementPolicy;
use App\Policies\SupplierPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Category::class,      CategoryPolicy::class);
        Gate::policy(Product::class,       ProductPolicy::class);
        Gate::policy(Supplier::class,      SupplierPolicy::class);
        Gate::policy(Customer::class,      CustomerPolicy::class);
        Gate::policy(PurchaseOrder::class, PurchaseOrderPolicy::class);
        Gate::policy(SalesOrder::class,    SalesOrderPolicy::class);
        Gate::policy(StockMovement::class, StockMovementPolicy::class);
    }
}