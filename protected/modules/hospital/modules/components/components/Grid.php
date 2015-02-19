<?php
class Grid extends CComponent {
    protected $columns = array();

    public function __construct($columns = array()) {
        $this->setColumns($columns);
    }

    public function setColumns($columns) {
        $this->columns = $columns;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function parse() {
        foreach($this->columns as &$column) {
            /**
             * Share all action with columns to groups:
             * 1) Data markers % %
             * 2) Function markers {{ | }}
             * Syntax sample : {{%data%|trim}}
             */
            if(isset($column['value'])) {
                $column['value'] = preg_replace('/%(.+?)%/', '$data->${1}', $column['value']); // lazy search
                $column['value'] = $this->applyFunctions($column['value']);
            }
        }

        return $this;
    }

    public function applyFunctions($value) {
        $valueChunked = array();
        preg_match('/\{\{(.*)\|(.*)\}\}/', $value, $valueChunked);

        if(count($valueChunked) > 0) {
            $data = $valueChunked[1];
            $functionsList = explode(',', $valueChunked[2]);
            $num = count($functionsList);
            for($i = 0; $i < $num; $i++) {
                $function = mb_strtolower($functionsList[$i]);
                $methodName = $function.'Function';
                $data = $this->$methodName($data);
            }
            $value = str_replace($valueChunked[0], $data, $value);
        }

        return $value;
    }

    protected function trimFunction($data) {
        return 'trim('.$data.')';
    }

    protected function intFunction($data) {
        return 'floor('.$data.')';
    }
}

?>