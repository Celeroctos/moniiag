<?php
class ProfileController extends Controller {
    public $layout = 'application.modules.settings.views.layouts.index';
    public $formModel = null;

    public function actionView() {
        $this->formModel = new FormProfileEdit();
        $this->setProfileValues();
        $this->render('view', array(
            'model' => $this->formModel,
            'avatarModel' => new FormAvatarEdit(),
			'avatarPath' => $this->getAvatarPath(),
        ));
    }

    // Заполнить модель профиля значениями
    private function setProfileValues() {
        if(Yii::app()->user->isGuest) {
            $req = new CHttpRequest();
            $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/'));
        }
        // Выбираем текущего пользователя
        $userModel = new User();
        $user = $userModel->getOne(Yii::app()->user->id);
        $this->formModel->username = $user['username'];
        $this->formModel->login = $user['login'];
    }

    public function actionEdit() {
        if(Yii::app()->user->isGuest) {
            $req = new CHttpRequest();
            $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/'));
        }
        $model = new FormProfileEdit();
        if(isset($_POST['FormProfileEdit'])) {
            $model->attributes = $_POST['FormProfileEdit'];
            if($model->validate()) {
                $user = User::model()->find('id=:id', array(':id' => Yii::app()->user->id));
                $user->username = $model->username;
                // Проверка логина на пустоту и повтор
                // Если юзер с таким логином уже есть, и он не тот, который редактируется - выводить сообщение
                $userCheckLogin = User::model()->find('login = :login', array(':login' => $model->login));
                if($userCheckLogin != null && $userCheckLogin->id != Yii::app()->user->id) {
                    echo CJSON::encode(array('success' => 'false',
                            'errors' => array(
                                array('Пользователь с таким логином уже существует!')
                            )
                        )
                    );
                    exit();
                }
                $user->login = $model->login;
                // Проверка пароля, если проставлен
                if(trim($model->password) != '') {
                    if($model->password != $model->passwordRepeat) {
                        echo CJSON::encode(array('success' => 'false',
                                'errors' => array(
                                    array('Пароль и повтор пароля не совпадают!')
                                )
                            )
                        );
                        exit();
                    }
                    if(mb_strlen(trim($model->password)) < 6) {
                        echo CJSON::encode(array('success' => 'false',
                                'errors' => array(
                                    array('Пароль не может быть меньше 6 символов!')
                                )
                            )
                        );
                        exit();
                    }
                    $user->password = crypt($model->password, $model->password);
                }

                if(!$user->save()) {
                    echo CJSON::encode(array('success' => 'false',
                            'errors' => array(
                                array('Невозможно сохранить отредактированную запись!')
                            )
                        )
                    );
                    exit();
                }

                Yii::app()->user->username = $model->username;
                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Профиль успешно отредактирован.'
                    )
                );
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    // Загрузка аватара
    public function actionAvatarUpload() {
        if(Yii::app()->user->isGuest) {
            $req = new CHttpRequest();
            $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/'));
        }
        $model = new FormAvatarEdit();
        if(isset($_POST['FormAvatarEdit'])) {
			$model->avatar = CUploadedFile::getInstance($model, 'avatar');
			if($model->validate()) {
				// Попробуем найти аватар в базе. Если он есть, значит нужно заменить имя файла и ссылку на файл. Если нет - создать новый.
				$fileModel = FileModel::model()->find('owner_id = :owner_id AND type = 0', array(':owner_id' => Yii::app()->user->id));
				if($fileModel == null) {
					$fileModel = new FileModel();
					$fileModel->owner_id = Yii::app()->user->id;
					$fileModel->type = 0;
				}
				$filenameGenered = md5(date('Y-m-d h:m:s').$model->avatar->name);
				$fileModel->filename = $filenameGenered;
				$fileModel->path = '/uploads/avatars/'.Yii::app()->user->id.'/'.$filenameGenered.'.'.$model->avatar->extensionName;
				if($fileModel->save()) {
					// Надо скопировать файл..
					// Создадим директории, если не существуют
					if(!file_exists(getcwd().'/uploads')) {
						mkdir(getcwd().'/uploads');
					}
					if(!file_exists(getcwd().'/uploads/avatars')) {
						mkdir(getcwd().'/uploads/avatars');
					}
					if(!file_exists(getcwd().'/uploads/avatars/'.Yii::app()->user->id)) {
						mkdir(getcwd().'/uploads/avatars/'.Yii::app()->user->id);
					}
					$model->avatar->saveAs(getcwd().$fileModel->path);
				}
			}
        }
		$req = new CHttpRequest();
		$req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/settings/profile/view'));
    }
	
	public function getAvatarPath() {
		$fileModel = FileModel::model()->find('owner_id = :owner_id AND type = 0', array(':owner_id' => Yii::app()->user->id));
		if($fileModel == null) {
			$path = '/uploads/avatars/default.png';
		} else {
            $path = $fileModel->path;
        }
		return $path;
	}
}
?>