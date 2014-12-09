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
                                             'data' => Yii::app()->request->baseUrl.''.Yii::app()->user->startpageUrl));
                    exit();
                } else {

                    $resultCode = 'loginError';
                    // анализируем код ошибки из экземпл€ра класса userIdentity
                    if ($userIdent->wrongLogin())
                    {
                        $resultCode = 'notFoundLogin';
                    }

                    if ($userIdent->wrongPassword())
                    {
                        $resultCode = 'wrongPassword';
                    }

                    echo CJSON::encode(array('success' => $resultCode,
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
	
	
	public function actionIsLogged() {
		echo CJSON::encode(
			array(
				'success' => !Yii::app()->user->getIsGuest(),
            )
		);
	}
}

?>
