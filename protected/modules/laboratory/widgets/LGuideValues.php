<?php

class LGuideValues extends LWidget {

    public $guide_id = null;

    public function run() {
        if (!$this->guide_id || $this->guide_id == -1) {
            throw new CException("Guide identification number must be valid \"{$this->guide_id}\"");
        }
        $this->render(__CLASS__, [
            "columns" => LGuideColumn::model()->findAll("guide_id = :guide_id", [
                ":guide_id" => $this->guide_id
            ])
        ], false);
    }

    public function renderField($type, $label) {
        return LFieldCollection::getCollection()->find($type)->renderEx(
            new CActiveForm(), new LFormModelAdapter([]), "", $label
        );
    }

    const DEFAULT_COUNT = 5;
} 