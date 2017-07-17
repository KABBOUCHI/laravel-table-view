<?php

namespace KABBOUCHI\TableView;


class TableViewColumn
{
    protected $title;

    private $propertyName;

    private $customValue;

    private $cast = null;

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
            $this->cast = $cast;
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

            return is_callable($closure) ? $closure($model) : self::getCastedValue($model->{$this->customValue});
        }
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function getCastedValue($value)
    {
        $array = explode('|', $this->cast);

        $type = $array[0];

        $options = count($array) <= 1 ? [] : explode(',', $array[1]);

        switch (strtolower($type)) {
            case 'boolean':
                if (count($options) < 2) return is_bool($value) ? ($value ? 'True' : 'False') : $value;

                return is_bool($value) ? ($value ? $options[1] : $options[0]) : $value;

            case 'image':
                if (count($options) < 2) return '<img src="' . $value . '">';

                $class = count($options) >= 3 ? $options[2] : '';

                return "<img src='{$value}' class='{$class}' width='{$options[0]}' height='{$options[1]}'>";
        }

        return $value;
    }
}