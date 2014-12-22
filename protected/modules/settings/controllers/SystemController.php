<?php
class SystemController extends Controller {
    public $formModel;
    public function actionView() {
        $this->formModel = new FormSystemEdit();
        $this->fillSystemModel();
        $this->render('view', array(
            'model' => $this->formModel
        ));
    }

    public function actionSettingsEdit() {
        $this->formModel = new FormSystemEdit();
        if(isset($_POST['FormSystemEdit'])) {
            $this->formModel->attributes = $_POST['FormSystemEdit'];
            if($this->formModel->validate()) {
                foreach($this->formModel->attributes as $key => $settingForm) {
                    $setting = Setting::model()->find('module_id = -1 AND name = :name', array(':name' => $key));
                    if($setting != null) {
                        $setting->value = $settingForm;
                        if(!$setting->save()) {
                            echo CJSON::encode(array('success' => 'false',
                                                     'errors' => $setting->errors));
                            exit();
                        }
                    }
                }
                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Настройки успешно изменены.'));
                exit();
            }
        }

        echo CJSON::encode(array('success' => 'false',
                                 'errors' => $this->formModel->errors));
    }
	
	public function actionSettingsJsonEdit() {
		if(!isset($_GET['module'], $_GET['values'])) {
			echo CJSON::encode(
				array(
					'success' => false,
					'error' => 'Нехватка данных!'
					)
				);
			exit();
		}

        foreach($_GET['values'] as $settingName => $value) {
            $setting = Setting::model()->find('module_id = :module_id AND name = :name', array(':name' => $settingName, ':module_id' => $_GET['module']));
			if($setting == null) {
				$setting = new Setting();
				$setting->name = $settingName;
				$setting->module_id = $_GET['module']; 
			} 
			
			$setting->value = $value;
			if(!$setting->save()) {
				echo CJSON::encode(array('success' => false,
										 'errors' => 'Не могу сохранить ключ '.$settingName));
				exit();
			}
        }
		
		echo CJSON::encode(
			array(
				'success' => true,
				'data' => array()
			)
		);
	}

    private function fillSystemModel() {
        $settings = $this->getSettings(-1);
        foreach($settings as $setting) {
            if($setting['name'] != null) {
                $this->formModel->{$setting['name']} = $setting['value'];
            } else {
                $this->formModel->{$setting['name']} = '';
            }
        }
    }

    private function getSettings($moduleId) {
        $filters = array(
            'groupOp' => 'AND',
            'rules' => array(
                array(
                    'field' => 'module_id',
                    'op' => 'eq',
                    'data' => $moduleId // Модуль расписания
                )
            )
        );
        $settingModel = new Setting();
        $settings = $settingModel->getRows($filters);
        return $settings;
    }
	
	public function actionGetSetEmptyTaps() {
		$conn = Yii::app()->db2;
		$conn2 = Yii::app()->db;
		
		$sql = 'ALTER TABLE dbo.t_tap_10874 DISABLE TRIGGER trt_tap_10874_update';
		$conn->createCommand($sql)->execute();
		
		$withoutCardsSql = "SELECT 
			t1.uid,
			t4.series_14820, 
			t4.number_12574, 
			t4.series_14820 + t4.number_12574,
			t1.fam_18565, 
			t1.im_53316, 
			t1.ot_48206 
		FROM t_patient_10905 t1
		JOIN t_policy_43176 t4 ON t4.patientuid_09882 = t1.uid
		WHERE NOT EXISTS(
			SELECT t2.uid 
			FROM t_book_65067 t2
			WHERE t2.patientuid_37756 = t1.uid
			AND t2.version_end = 9223372036854775807
		)
		AND EXISTS(
			SELECT t3.*
			FROM t_tap_10874 t3
			WHERE t3.patientuid_40511 = t1.uid
				AND t3.fillingdate_36966 >= '2014-01-10 00:00:00' 
				AND t3.version_end = 9223372036854775807
		)
		AND t1.version_end = 9223372036854775807
		AND t4.version_end = 9223372036854775807
		ORDER BY t1.uid DESC";
		
		$withoutCards = $conn->createCommand($withoutCardsSql)->queryAll();
		print_r($withoutCards);
		
		foreach($withoutCards as $element) {
			$sql = "SELECT o.last_name, o.first_name, o.middle_name, o.oms_series_number, m.card_number FROM mis.tasu_greetings_buffer tgb 
			LEFT JOIN mis.tasu_fake_greetings tfg ON tgb.fake_id = tfg.id
			LEFT JOIN mis.medcards m ON m.card_number = tfg.card_number
			LEFT JOIN mis.oms o ON o.id = m.policy_id
			WHERE tfg.greeting_date > '2014-09-30'
			AND o.oms_series_number = '".$element['series_14820'].$element['number_12574']."'
			OR(o.oms_series = '".$element['series_14820']."' AND o.oms_number = '".$element['number_12574']."')";

			$inMis = $conn2->createCommand($sql)->queryAll();
			if(count($inMis) > 0) {
				$sql = "SELECT t2.* 
						FROM t_book_65067 t2
						WHERE t2.number_50713 = '".$inMis[0]['card_number']."'";
				
				$tasuTbook = $conn->createCommand($sql)->queryAll();
				
				// А какой пациент записан на эту карту...?
				$whichPatientSql = "
				SELECT 
					t1.uid,
					t1.fam_18565, 
					t1.im_53316, 
					t1.ot_48206 
				FROM t_patient_10905 t1
				WHERE t1.version_end = 9223372036854775807
				AND t1.uid = ".$tasuTbook[0]['patientuid_37756'];
				
				$tasuWhichPatient = $conn->createCommand($whichPatientSql)->queryAll();
						
				// Смотрим TAP на стороне ТАСУ
				$sql = "SELECT t3.*
						FROM t_tap_10874 t3
						WHERE t3.patientuid_40511 = ".$element['uid']."
						AND t3.fillingdate_36966 >= '2014-01-10 00:00:00'";
				
				$tasuUnlinkedTap = $conn->createCommand($sql)->queryAll();
				echo "<pre>";
				echo "Это тот пациент, что без карты (ТАСУ): <br />";
				print_r($element);
				echo "Это карта того же пациента (реальная, в ТАСУ):<br />";
				print_r($tasuTbook);
				echo "Это то, на какого пациента указывает реальная карта в ТАСУ:<br />";
				print_r($tasuWhichPatient);
				echo "А это - непривязанный ТАП в ТАСУ:<br />";
				print_r($tasuUnlinkedTap);
				
				foreach($tasuUnlinkedTap as $t) {
					$sql = 'SELECT * FROM t_tap_10874 
							WHERE uid = '.$t['uid'].'
							AND version_end = 9223372036854775807';
					
					$result = $conn->createCommand($sql)->queryAll();
					echo "Это то, что обновляем:<br />";
					print_r($result);
					
					$sql = 'UPDATE t_tap_10874 
							SET patientuid_40511 = '.$tasuWhichPatient[0]['uid'].' 
							WHERE uid = '.$t['uid'].'
							AND version_end = 9223372036854775807';

					$result = $conn->createCommand($sql)->execute();	
				}
				echo "</pre>";
				//exit();
			}
		}
	}
	
	public function actionGetSetOmsSerie() {
		$conn = Yii::app()->db2;
		
		$sql = 'ALTER TABLE dbo.t_tap_10874 DISABLE TRIGGER trt_tap_10874_update';
		$conn->createCommand($sql)->execute();
		
		$sql = 'ALTER TABLE dbo.t_policy_43176 DISABLE TRIGGER trt_policy_43176_delete';
		$conn->createCommand($sql)->execute();
		
		$sql = "SELECT 
				t1.*,
				t2.*,
				t2.uid as PatientUID,
				t3.*
			FROM t_book_65067 t1
			JOIN t_patient_10905 t2 ON t1.patientuid_37756 = t2.uid
			JOIN t_policy_43176 t3 ON t3.patientuid_09882 = t2.uid
			WHERE t1.number_50713 LIKE '%/14'
			AND t1.version_end = 9223372036854775807
			AND t2.version_end = 9223372036854775807
			AND t3.version_end = 9223372036854775807
			AND t3.series_14820 LIKE '____'";
		
		$patientsData = $conn->createCommand($sql)->queryAll();
		
		echo "<pre>";
		echo "Количество строк ".count($patientsData)."<br />";
		foreach($patientsData as $patientData) {
			$sql = "SELECT t1.* 
					FROM t_policy_43176 t1
					WHERE t1.patientuid_09882 = ".$patientData['PatientUID']."
					AND t1.version_end = 9223372036854775807
					AND t1.series_14820 LIKE '____'";
			$policesData = $conn->createCommand($sql)->queryAll();

			$sql = "SELECT t1.* 
					FROM t_policy_43176 t1
					WHERE 
					t1.version_end = 9223372036854775807
					AND t1.series_14820 = '".mb_substr($patientData['series_14820'], 0, 2)."-".mb_substr($patientData['series_14820'], 2, 2)."'
						AND t1.number_12574 = '".$patientData['number_12574']."'";
			
			
			$policesWithSepData = $conn->createCommand($sql)->queryAll();
			if(count($policesWithSepData) > 0) {
				echo "Данные по полисам на пациента без дефиса: ";
				print_r($policesData);
		
				echo "Данные по полисам с таким же номером, но с сепаратором: <br>";
				print_r($policesWithSepData);
				
				$sql = 'UPDATE t_tap_10874 
						SET policyuid_53853 = '.$policesWithSepData[0]['uid'].' 
						WHERE policyuid_53853 = '.$policesData[0]['uid'].'
						AND version_end = 9223372036854775807';
				
				$result = $conn->createCommand($sql)->execute();
				
				$policesData[0]['uid'];
				$sql = 'DELETE FROM t_policy_43176
						WHERE uid = '.$policesData[0]['uid'].'
						AND version_end = 9223372036854775807';
				
				$result = $conn->createCommand($sql)->execute();
				echo "---------------";
			}
	
		}
		
		$sql = 'ALTER TABLE dbo.t_tap_10874 ENABLE TRIGGER trt_tap_10874_update';
		$conn->createCommand($sql)->execute();
		
		$sql = 'ALTER TABLE dbo.t_policy_43176 ENABLE TRIGGER trt_policy_43176_delete';
		$conn->createCommand($sql)->execute();
		
		echo "</pre>";
	
	}
	
	
	
	public function actionGetEmptyDul() {

		$dulArr = array(
			array("46  99","341802", "46 99"),
			array("70  01","195616", "70 01")
		);
		
		$conn = Yii::app()->db2;
		
		$sql = 'ALTER TABLE dbo.t_tap_10874 DISABLE TRIGGER trt_tap_10874_update';
		$conn->createCommand($sql)->execute();
		
		$sql = 'ALTER TABLE dbo.t_dul_44571 DISABLE TRIGGER trt_dul_44571_delete';
		$conn->createCommand($sql)->execute();
		
		echo "<pre>";

		foreach($dulArr as $key => $arr) {
			$sql = "SELECT 
					patientuid_40511,
					duluid_44636,
					addressuid_30547,
					policyuid_53853,
					bookuid_60769,
					t3.uid as TapUID
				FROM
					t_dul_44571 t1
				LEFT JOIN t_tap_10874 t3 ON t3.duluid_44636 = t1.uid
				WHERE
					t1.dulseries_30145 = '".$arr[0]."'
				AND	t1.dulnumber_50657 = '".$arr[1]."'
				AND t1.version_end = 9223372036854775807";

			$dulData = $conn->createCommand($sql)->queryAll();
			
			if(count($dulData) > 0) {
				echo "Данные по ДУЛ:<br />";
				print_r($dulData);
				
				$sql = "SELECT
							t2.fam_18565, 
							t2.im_53316, 
							t2.ot_48206, 
							t4.dulseries_30145,
							t4.dulnumber_50657,
							t4.uid as dulUID,
							t2.uid as PatientUID,
							t3.uid as TBookUID,
							t5.uid as TPolicyUID
						FROM t_patient_10905 t2
						JOIN t_dul_44571 t4 ON t4.patientuid_53984 = t2.uid
						JOIN t_book_65067 t3 ON t3.patientuid_37756 = t2.uid
						JOIN t_policy_43176 t5 ON t5.patientuid_09882 = t2.uid
						AND t2.version_end = 9223372036854775807
						AND t4.dulseries_30145 = '".$arr[2]."'
						AND	t4.dulnumber_50657 = '".$arr[1]."'";
									
				$patientData = $conn->createCommand($sql)->queryAll();
				
				echo "Данные по пациенту:<br />";
				print_r($patientData);
				
				//exit(1);
				if(count($patientData) > 0) {
					$sql = 'UPDATE t_tap_10874 
							SET 
								duluid_44636 = '.$patientData[0]['dulUID'].'
							WHERE uid = '.$dulData[0]['TapUID'];
							
					$conn->createCommand($sql)->execute();
					
					$sql = "DELETE FROM t_dul_44571 
							WHERE
								dulseries_30145 = '".$arr[0]."'
								AND	dulnumber_50657 = '".$arr[1]."'
								AND version_end = 9223372036854775807";
							
					$conn->createCommand($sql)->execute();
				}
				echo "-------------------";
		
			}
		}
		echo "</pre>";
		
		$sql = 'ALTER TABLE dbo.t_tap_10874 ENABLE TRIGGER trt_tap_10874_update';
		$conn->createCommand($sql)->execute();
		
		$sql = 'ALTER TABLE dbo.t_dul_44571 ENABLE TRIGGER trt_dul_44571_delete';
		$conn->createCommand($sql)->execute();
	}
	
	public function actionGetWarnings() {
		$arr = array(
			'18632/14',
			'11968/14',
			'17061/14'
		);
				
		$conn = Yii::app()->db2;
		echo "<pre>";
		
		foreach($arr as $medcard) {
			$sql = "SELECT t1.*, t2.*, t2.uid as PatientUID
				FROM t_book_65067 t1
				LEFT JOIN t_patient_10905 t2 ON t2.uid = t1.patientuid_37756
				WHERE t1.version_end = 9223372036854775807
				AND t2.version_end = 9223372036854775807
				AND t1.number_50713 = '".$medcard."'";
			
			$result = $conn->createCommand($sql)->queryAll();
			echo "Медкарты с таким номером:<br />";
			
			print_r($result);
			
			$sql = "SELECT t1.* 
					FROM t_policy_43176 t1
					WHERE t1.patientuid_09882 = ".$result[0]['PatientUID'];
			$result = $conn->createCommand($sql)->queryAll();
			echo "Полис с такой картой:<br />";
			
			print_r($result);
			echo "-------------";
			
		}

		echo "</pre>";
	}
	
	/*public function actionGetSetDoubleSpacesDul() {
		$conn = Yii::app()->db2;
		
		$sql = 'ALTER TABLE dbo.t_dul_44571 DISABLE TRIGGER trt_dul_44571_update';
		$conn->createCommand($sql)->execute();
		
		$conn = Yii::app()->db2;
		
		$sql = "SELECT * FROM t_dul_44571
				WHERE 
					dulseries_30145 LIKE '__  __'
					AND version_end = 9223372036854775807";
		$duls = $conn->createCommand($sql)->queryAll();
		echo "<pre>";
		foreach($duls as $dul){
			$dulWithoutDoubleSpace = mb_substr($dul['dulseries_30145'], 0, 2).' '.mb_substr($dul['dulseries_30145'], 4, 3);
			print_r($dul);
			echo "DUL-серия без лишнего пробела:";
			echo $dulWithoutDoubleSpace."<br />";
			
			// Ищем дул с такой серией: он уже есть.
			$sql = "SELECT t1.* 
					FROM t_dul_44571 t1
					JOIN t_patient_10905 t2 ON t2.uid = t1.patientuid_53984
					WHERE 
						t1.dulseries_30145 = '".$dulWithoutDoubleSpace."'
						AND t1.dulnumber_50657 = '".$dul['dulnumber_50657']."'
						AND t1.version_end = 9223372036854775807
						AND t2.version_end = 9223372036854775807
						AND t1.dulowner_fam_25160 IS NOT NULL";
			
			$issetDuls = $conn->createCommand($sql)->queryAll();
			if(count($issetDuls) == 1) {
				echo "Существующий DUL:<br />";
				print_r($issetDuls);
				
				$sql = "SELECT
							t1.*
						FROM t_dul_44571 t1
						AND t1.version_end = 9223372036854775807
						AND t1.duluid_44636 = $dul";
				
			}
			echo "--------------<br>";
		}
		echo "</pre>";
		$sql = 'ALTER TABLE dbo.t_dul_44571 ENABLE TRIGGER trt_dul_44571_update';
		$conn->createCommand($sql)->execute();
	
	}*/
	
	
}
?>