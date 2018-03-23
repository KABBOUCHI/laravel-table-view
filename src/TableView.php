<?php

namespace KABBOUCHI\TableView;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TableView
{
    public $dataTable = false;

    protected $id;
    protected $collection;
    protected $columns = [];
    protected $classes = 'table';
    protected $paginator = null;
    protected $appendsQueries = false;
    private $searchFields = [];

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function render($id = null)
    {
        if (count($this->columns) == 0) {
            if ($this->collection->count() > 0) {
                $array = $this->collection->first()->toArray();

                foreach ($array as $key => $value) {
                    $this->column(str_replace('_', ' ', ucfirst($key)), $key);
                }
            }
        }
        $this->id = $id != null ? $id : 'table-'.str_random(6);

        return new HtmlString(view('tableView::index', ['tableView' => $this])->render());
    }

    public function column($title, $value = null, $cast = null)
    {
        if ((is_string($title) && ! $value)) {
            $attr = explode(':', $title);
            $value = $title;
            $title = str_replace('_', ' ', ucfirst($attr[0]));
        }

        if (is_string($value)) {
            $attr = explode(':', $value);
            $value = $attr[0];

            if (isset($attr[1]) && str_contains($attr[1], 'search')) {
                $this->searchFields[] = $value;
            }
        }

        $column = new TableViewColumn($title, $value, $cast);

        $this->columns[] = $column;

        return $this;
    }

    public function useDataTable()
    {
        $this->dataTable = true;

        return $this;
    }

    public function paginate($perPage = 15, $page = null, $options = [])
    {
        $this->dataTable = false;

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $this->applySearchFilter();

        $this->paginator = new LengthAwarePaginator($this->collection->forPage($page, $perPage),
            $this->collection->count(), $perPage, $page, $options);

        return $this;
    }

    private function applySearchFilter()
    {
        if (count($this->searchableFields()) && ! empty($this->searchQuery())) {
            $this->collection = $this->collection->filter(function ($data) {
                foreach ($this->searchableFields() as $field) {
                    if (str_contains(strtolower($data->{$field}), strtolower($this->searchQuery()))) {
                        return true;
                    }
                }

                return false;
            });
        }
    }

    public function searchableFields()
    {
        return $this->searchFields;
    }

    public function searchQuery()
    {
        return Request::get('q');
    }

    public function id()
    {
        return $this->id;
    }

    public function appendsQueries($append = true)
    {
        $this->appendsQueries = $append;

        return $this;
    }

    public function data()
    {
        if ($this->hasPagination()) {
            $params = [];
            if ($this->appendsQueries) {
                if (is_array($this->appendsQueries)) {
                    $params = App::make('request')->query->get($this->appendsQueries);
                } else {
                    $params = App::make('request')->query->all();
                }
            }

            return $this->paginator->appends($params)->setPath('');
        }

        if (! $this->dataTable) {
            $this->applySearchFilter();
        }

        return $this->collection;
    }

    public function hasPagination()
    {
        return (bool) $this->paginator;
    }

    public function columns()
    {
        return $this->columns;
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
