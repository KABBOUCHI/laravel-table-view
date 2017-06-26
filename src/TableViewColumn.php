<?php

namespace KABBOUCHI\TableView;


class TableViewColumn
{
    protected $title;

    private $propertyName;

    private $customValue;

    public function __construct($title, $value)
    {
        if (is_null($value)) {
            $value = $title;
            $title = '';
        }
        $this->title = ($title === false) ? '' : $title;

        if (is_string($value)) {
            $this->propertyName = $value;
        } else {
            $this->customValue = $value;
        }
    }

    public function title()
    {
        return $this->title;
    }

    public function propertyName()
    {
        return $this->propertyName;
    }

    public function rowValue($model)
    {
        if (!isset($this->customValue)) {
            return $model->{$this->propertyName};
        } else {
            $closure = $this->customValue;

            return $closure($model);
        }
    }
}