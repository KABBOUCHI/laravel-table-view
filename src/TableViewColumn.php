<?php

namespace KABBOUCHI\TableView;


class TableViewColumn
{
    protected $title;

    private $propertyName;

    private $customValue;

    public function __construct($title, $value, $cast = null)
    {
        if (is_null($value)) {
            $value = $title;
            $title = '';
        }
        $this->title = ($title === false) ? '' : $title;

        if (is_string($value) && $cast == null) {
            $this->propertyName = $value;
        } else {
            if ($cast) {
                settype($value, $cast);
            }
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

            return is_callable($closure) ? $closure($model) : self::getCastedValue($closure);
        }
    }

    /**
     * @param mixed $value
     * @return string
     */
    public static function getCastedValue($value)
    {
        return (is_bool($value) ? ($value ? 'True' : 'False') : $value);
    }
}