<?php namespace KABBOUCHI\TableView;


use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class TableView
{
    protected $id;
    protected $collection;
    protected $columns = [];
    protected $classes = 'table';

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function render($id = null)
    {
        $this->id = $id ?? 'table-' . str_random(6);

        return new HtmlString(view('tableView::index', ['tableView' => $this])->render());
    }

    public function id()
    {
        return $this->id;
    }

    public function data()
    {
        return $this->collection;
    }

    public function columns()
    {
        return $this->columns;
    }

    public function column($title, $value = null)
    {
        $column = new TableViewColumn($title, $value);

        $this->columns[] = $column;

        return $this;
    }

    public function setTableClass($classes)
    {
        $this->classes = $classes;

        return $this;
    }

    public function getTableClass()
    {
        return $this->classes;
    }
}