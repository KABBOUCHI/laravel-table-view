<?php namespace KABBOUCHI\TableView;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class TableView
{
    public $dataTable = false;

    protected $id;
    protected $collection;
    protected $columns = [];
    protected $classes = 'table';
    protected $paginator = null;

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

                    $this->column(ucfirst($key), $key);
                }
            }

        }
        $this->id = $id != null ? $id : 'table-' . str_random(6);

        return new HtmlString(view('tableView::index', ['tableView' => $this])->render());
    }

    public function column($title, $value = null, $cast = null)
    {
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
        $this->paginator = new LengthAwarePaginator($this->collection->forPage($page, $perPage),
            $this->collection->count(), $perPage, $page, $options);


        return $this;
    }
    public function hasPagination()
    {
        return !! $this->paginator;
    }
    public function id()
    {
        return $this->id;
    }

    public function data()
    {
        if ($this->hasPagination())
        {
            $params = App::make("request")->query->all();
            return $this->paginator->appends($params)->setPath('');
        }

        return $this->collection;
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