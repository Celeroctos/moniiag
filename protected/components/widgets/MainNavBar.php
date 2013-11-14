<?php
class MainNavBar extends CWidget {
    public function run() {
        if(Yii::app()->user->isGuest) {
            $loginForm = new FormLogin();
            $this->render('application.components.widgets.views.MainNavBarUnlogged', array(
                'loginFormModel' => $loginForm
            ));
        } else {
            $this->render('application.components.widgets.views.MainNavBarLogged', array(
                'userName' => Yii::app()->user->username
            ));
        }
    }
}

?>