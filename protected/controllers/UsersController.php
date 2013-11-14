<?php
class UsersController extends Controller {
    public function actionLogin() {
        if(isset($_POST['FormLogin'])) {
            $formModel = new FormLogin();
            $formModel->attributes = $_POST['FormLogin'];

            if($formModel->validate()) {
                $userIdent = new UserIdentity($formModel->login, $formModel->password);
                if($userIdent->authenticate()) {
                    Yii::app()->user->login($userIdent);
                    echo CJSON::encode(array('success' => 'true',
                                             'msg' => 'Вы успешно авторизовались в системе.'));
                } else {
                    echo CJSON::encode(array('success' => 'notfound',
                                             'errors' => $userIdent->errorMessage));
                }
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $formModel->getErrors()));
            }
        } else {
            echo CJSON::encode(array('success' => 'false',
                                     'text' => ''));
        }
    }

    public function actionLogout() {
        Yii::app()->user->logout();
        echo CJSON::encode(array('success' => 'true',
                                 'msg' => ''));
    }
}

?>
