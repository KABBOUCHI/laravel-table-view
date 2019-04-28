<?php

use KABBOUCHI\TableView\TableView;

if (! function_exists('tableView')) {
    /**
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Builder $data
     * @return TableView
     */
    function tableView($data)
    {
        return new TableView($data);
    }
}
