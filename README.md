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
    $users = User::all();
    
    tableView($user)->render();

```

```php
    $users = User::all();
    
    tableView($user)
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
    
    $table = tableView($user)
             ->setTableClass('table table-striped')
             ->useDataTable(); // You need to add @stack('scripts') and @stack('styles') in your main blade template
    
    // view
     $table->render();
```

```php

    // tableView with server side pagination
    
    // controller
    $users = User::all();
   
    $table = tableView($user)
               ->column('ID', 'id')
               ->column('Active', 'active','boolean')
               ->column('Name', 'name:search') // enable search for names
               ->column('Created At', function ($model) {
                               return $model->created_at->diffForHumans();
                })
                ->appendsQueries(true) // or pass an Array for specific queries e.g: ['foo','bar']
                ->paginate(); // default 15
            
    // view
    $table->render();
```



## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
