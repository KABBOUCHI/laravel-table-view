# laravel-table-view

Laravel 5 Package for easily displaying table views for Eloquent Collections with search and sort functionality built in.

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
           ->column('Name', 'name')
           ->column('Created At', function ($model) {
                           return $model->created_at->diffForHumans();
            })
            ->render();
```

```php
    // controller
    $users = collect([
       (object) ["id" => 1, "user" => "User 1"],
       (object) ["id" => 2, "user" => "User 2"],
       (object) ["id" => 3, "user" => "User 3"],
       (object) ["id" => 4, "user" => "User 4"]
    
    ]);
    
    $table = tableView($user)
             ->setTableClass('table table-striped');
    
    // view
     $table->render();
```



## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.