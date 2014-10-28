<?php
class UsersController extends Controller {
    public function actionLogin() {
        if(isset($_POST['FormLogin'])) {
            $formModel = new FormLogin();
            $formModel->attributes = $_POST['FormLogin'];
            if($formModel->validate()) {
                $userIdent = new UserIdentity($formModel->login, $formModel->password);
                if($userIdent->authenticateStep1()) {
                    Yii::app()->user->login($userIdent);
					if(isset(Yii::app()->user->doctorId) && Yii::app()->user->getState('doctorId', -1) != -1) { // Если не требуется второго шага аутентификации..
						echo CJSON::encode(array('success' => 'true',
												 'data' => Yii::app()->request->baseUrl.''.Yii::app()->user->startpageUrl));
						exit();
					} else {
						echo CJSON::encode(array(
							'success' => 'true',
							'data' => Doctor::model()->findAll('user_id = :user_id', array(':user_id' => Yii::app()->user->id))
						));
						exit();
					}
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
	
	public function actionLoginStep2() {
		if(isset($_POST['FormChooseEmployee']) && Yii::app()->request->getIsAjaxRequest()) {
			$form = new FormChooseEmployee();
			$form->attributes = $_POST['FormChooseEmployee'];
			if($form->validate()) {
				$userIdent = new UserIdentity(Yii::app()->user->login, Yii::app()->user->password);
				// На всякий случай ещё раз проходим первую стадию аутентификации
				if($userIdent->authenticateStep1() && $userIdent->authenticateStep2(false, $form)) {;
					echo CJSON::encode(array(
						'success' => 'true',
						'data' => Yii::app()->request->baseUrl.''.Yii::app()->user->startpageUrl
						)
					);
					exit();
				}
			} else {
				echo CJSON::encode(array(
					'success' => 'false',
					'errors' => array(
						'employee' => array(
							'Неверный формат сотрудника!'
						)
					)
				));
			}
		}
	}
}

?>
