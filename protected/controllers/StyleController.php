<?php
class StyleController extends Controller {
    public function actionChangeFontSize($size) {
        $session = new CHttpSession();
        $session->open();
        $session['fontSize'] = $size;
        Yii::app()->user->fontSize = $size;
        echo CJSON::encode(array(
            'success' => true
        ));
    }
}
?>