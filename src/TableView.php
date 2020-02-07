<?php

namespace KABBOUCHI\TableView;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class TableView implements Htmlable
{
    use Macroable {
        __call as macroCall;
    }

    public $dataTable = false;

    protected $id;
    /**
     * @var Collection
     */
    protected $collection;
    protected $columns = [];
    protected $classes = 'table';
    protected $paginator = null;
    protected $appendsQueries = false;
    protected $tableRowAttributes;
    protected $tableBodyClass;
    protected $childDetailsCallback = null;

    /** @var Builder */
    protected $builder;

    private $searchFields = [];

    public function __construct($data)
    {
        if ($data instanceof Collection) {
            $this->collection = $data;
        }

        if ($data instanceof Builder) {
            $this->builder = $data;
        }
    }

    public function childDetails($callback)
    {
        $this->childDetailsCallback = $callback;

        return $this;
    }

    public function hasChildDetails()
    {
        return (bool) $this->childDetailsCallback;
    }

    public function getChildDetails($model)
    {
        $closure = $this->childDetailsCallback;

        $html = $closure instanceof Closure ? $closure($model) : '';

        if ($html instanceof View) {
            $html = $html->render();
        }

        return $html;
    }

    public function useDataTable()
    {
        $this->dataTable = true;
        $this->paginator = null;

        return $this;
    }

    public function paginate($perPage = 15, $page = null, $options = [])
    {
        $this->dataTable = false;

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $this->applySearchFilter();

        if ($this->collection) {
            $this->paginator = new LengthAwarePaginator(
                $this->collection->forPage($page, $perPage),
                $this->collection->count(),
                $perPage, $page, $options);
        } else {
            $this->paginator = new LengthAwarePaginator(
                $this->builder->forPage($page, $perPage)->get(),
                $this->builder->count(),
                $perPage, $page, $options);
        }

        return $this;
    }

    private function applySearchFilter()
    {
        if (count($this->searchableFields()) && ! empty($this->searchQuery())) {
            if ($this->collection) {
                $this->collection = $this->collection->filter(function ($data) {
                    foreach ($this->searchableFields() as $field) {
                        if (Str::contains(strtolower($data->{$field}), strtolower($this->searchQuery()))) {
                            return true;
                        }
                    }

                    return false;
                });
            }

            if ($this->builder) {
                $keyword = strtolower($this->searchQuery());

                $this->builder->where(function ($query) use ($keyword) {
                    foreach ($this->searchableFields() as $field) {
                        $query->orWhere($field, 'LIKE', "%$keyword%");
                    }
                });
            }
        }
    }

    public function setSearchableFields($fields = [])
    {
        $this->searchFields = $fields;

        return $this;
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

        if ($this->collection) {
            return $this->collection;
        }

        return $this->builder->get();
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

    public function getTableRowAttributes($model = null)
    {
        if (is_array($this->tableRowAttributes)) {
            return $this->tableRowAttributes;
        }

        $closure = $this->tableRowAttributes;

        return $closure instanceof Closure ? $closure($model) : [];
    }

    public function setTableRowAttributes($data)
    {
        $this->tableRowAttributes = $data ?? [];

        return $this;
    }

    public function setTableBodyClass($class)
    {
        $this->tableBodyClass = $class;

        return $this;
    }

    public function geTableBodyClass()
    {
        return $this->tableBodyClass;
    }

    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->render();
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
        $this->id = $id != null ? $id : 'table-'.Str::random(6);

        return new HtmlString(view('tableView::index', ['tableView' => $this])->render());
    }

    public function column($title, $value = null, $cast = null, $cssClasses = null)
    {
        if ((is_string($title) && ! $value)) {
            $attr = explode(':', $title);
            $attr = explode('.', $attr[0]);

            $value = $title;
            $title = str_replace('_', ' ', ucfirst($attr[0]));
        }

        if (is_string($value)) {
            $attr = explode(':', $value);
            $value = $attr[0];

            if (isset($attr[1]) && Str::contains($attr[1], 'search')) {
                $this->searchFields[] = $value;
            }
        }

        $column = new TableViewColumn($title, $value, $cast, $cssClasses);

        $this->columns[] = $column;

        return $this;
    }

    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }
    }
}
