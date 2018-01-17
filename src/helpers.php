<?php

use Illuminate\Support\Collection;
use KABBOUCHI\TableView\TableView;

if (! function_exists('tableView')) {
    function tableView(Collection $collection)
    {
        return new TableView($collection);
    }
}
