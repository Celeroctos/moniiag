<?php
class QuickPanelController extends Controller {
    public function actionRemoveElement($href) {
        $userId = Yii::app()->user->id;
        QuickPanelIcon::model()->deleteAll('href = :href AND user_id = :user_id', array(':href' => $href, ':user_id' => $userId));
        echo CJSON::encode(array('success' => true,
                                 'data' => array()));
    }

    public function actionAddElement($href, $icon) {
        // Проверим наличие иконки в базе: если есть, добавляеть не надо
        $iconSearched = QuickPanelIcon::model()->find('href = :href AND icon = :icon', array(':href' => $href, ':icon' => $icon));
        if($iconSearched != null) {
            echo CJSON::encode(array('success' => false,
                                     'error' => 'Иконка существует.'));
            exit();
        }
        $iconModel = new QuickPanelIcon;
        $iconModel->href = $href;
        $iconModel->icon = $icon;
        $iconModel->user_id = Yii::app()->user->id;
        if(!$iconModel->save()) {
            echo CJSON::encode(array('success' => false,
                                     'error' => 'Не могу сохранить иконку на панели быстрого запуска!'));
            exit();
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => array()));
    }
}

?>