<?php
class MainNavBar extends CWidget {
    private $months = array(
        'января',
        'февраля',
        'марта',
        'апереля',
        'мая',
        'июня',
        'июля',
        'августа',
        'сентября',
        'октября',
        'ноября',
        'декабря'
    );
    private $weekdays = array(
        'воскресенье',
        'понедельник',
        'вторник',
        'среда',
        'четверг',
        'пятница',
        'суббота'
    );
    public function run() {
        $answer = array(
            'monthDesc' => $this->months[date('n') - 1],
            'day' => date('j'),
            'weekdayDesc' => $this->weekdays[date('w')],
            'time' => date('G:i'),
            'year' => date('Y')
        );
        if(Yii::app()->user->isGuest) {
            $loginForm = new FormLogin();
            $answer['loginFormModel'] = $loginForm;
            $this->render('application.components.widgets.views.MainNavBarUnlogged', $answer);
        } else {
            $answer['userName'] = Yii::app()->user->username;
            $this->render('application.components.widgets.views.MainNavBarLogged', $answer);
        }
    }
}

?>