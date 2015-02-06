<?php

class LGuideValueEditor extends LWidget {

    public $guide_id = null;

    public function run() {
        if (!$this->guide_id || $this->guide_id == -1) {
            throw new CException("Guide identification number must be valid \"{$this->guide_id}\"");
        }
        $this->render(__CLASS__, [
            "columns" => LGuideColumn::model()->populateRecords(
                LGuideColumn::model()->findOrdered("guide_id = :guide_id", [
                    ":guide_id" => $this->guide_id
                ])
            ),
            "values" => LGuide::model()->findValues($this->guide_id)
        ], false);
    }

    public function renderField($type, $label, $value = null, $guideId = -1, $displayId = -1) {
        if (($type == "dropdown" || $type == "multiple") && $guideId != -1) {
            if ($displayId != -1) {
                $array = LGuide::model()->findValuesWithDisplay(
                    $guideId, $displayId
                );
                if ($type == "dropdown") {
                    $data = [ -1 => "Нет" ];
                }
                foreach ($array as $row) {
                    $data[$row["id"]] = $row["value"];
                }
            } else {
                $data = [
                    -1 => "Не настроен параметр отображения"
                ];
            }
        } else {
            $data = [];
        }
        return LFieldCollection::getCollection()->find($type)->renderEx(
            new CActiveForm(), new LFormModelAdapter(), "", $label, $value, $data
        );
    }

    const DEFAULT_COUNT = 1;
} 