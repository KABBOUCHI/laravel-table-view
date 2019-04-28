# laravel-table-view

Laravel 5 Package for easily displaying table views for Eloquent Collections.

Installation
----
 
``` bash
composer require kabbouchi/laravel-table-view
```
Next, you must install the service provider:

```php
// config/app.php
'providers' => [
    ...
    KABBOUCHI\TableView\TableViewServiceProvider::class,
];
```

You can publish and add custom styles for table view:
```bash
php artisan vendor:publish --provider="KABBOUCHI\TableView\TableViewServiceProvider" --tag="tableView"
```

## Usage

```php
    // Collection
    $users = User::all();
    
    // or
    
    // Builder
    $users = User::query(); //  User::where('active',true) ...
    
    tableView($users)->render();

```

```php
    $users = User::all();
    
    tableView($users)
           ->column('ID', 'id')
           ->column('Active', 'active','boolean')
           ->column('Featured', 'active','boolean|No,Yes')
           ->column('Photo', 'photo','image')
           ->column('Avatar', 'avatar','image|200,200')
           ->column('full_name') // ->column('Full name','full_name')
           ->column('Created At', function ($model) {
                           return $model->created_at->diffForHumans();
            })
            ->render();
```

```php
    // tableView with client side pagination (dataTable)
    
    // controller
    $users = collect([
       (object) ["id" => 1, "user" => "User 1"],
       (object) ["id" => 2, "user" => "User 2"],
       (object) ["id" => 3, "user" => "User 3"],
       (object) ["id" => 4, "user" => "User 4"]
    
    ]);
    
    $table = tableView($users)
             ->setTableClass('table table-striped')
             ->useDataTable(); // You need to add @stack('scripts') and @stack('styles') in your main blade template
    
    // view
     $table->render();
```

```php

    // tableView with server side pagination
    
    // controller
    $users = User::all();
   
    $table = tableView($users)
               ->column('ID', 'id')
               ->column('Active', 'active','boolean')
               ->column('Country', 'country.name') // $user->country->name
               ->column('Name', 'name:search') // enable search for names
               ->column('Created At', function ($model) {
                 return $model->created_at->diffForHumans();
                })
               ->childDetails(function (User $user) {
                 return view('partials.user-details',compact('user')); // return view or string
                });
                ->appendsQueries(true) // or pass an Array for specific queries e.g: ['foo','bar']
                ->paginate(); // default 15
            
    // view
    $table->render();
```

Specify custom attribute for each `tr`
```php
    $table = tableView(User::all())
                ->setTableRowAttributes([
                    'class' => 'tr-class'
                ]);
```

Specify custom attribute for each `tr` using closure 
```php
    $table = tableView(Category::all()
                ->setTableBodyClass('sortable')
                ->setTableRowAttributes(function (Category $categ) {
                    return [
                        'data-order-id' => $categ->order_index
                    ];
                });
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
