<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/sidemenu.js"></script>
<style>
    #mainSideMenu li li {
        font-size: <?php echo isset(Yii::app()->user->fontSize) ? Yii::app()->user->fontSize - 1 : 11; ?>px !important;
    }
</style>
<div role="complementary" class="bs-sidebar hidden-print" >
<ul class="nav bs-sidenav" id="mainSideMenu">
<!--<li <?php echo $controller == 'index' && $module == null ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/icon_sample.png" width="32" height="32" alt="" />Главная', array('/')) ?>
        </li>-->
<?php if(Yii::app()->user->checkAccess('menuRegister')) { ?>
    <li <?php echo $module == 'reception' && (!isset($_GET['callcenter']) || $_GET['callcenter'] != 1) ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/register.png" width="32" height="32" alt="" />Регистратура', array('#')) ?>
        <ul class="nav">
            <?php if(Yii::app()->user->checkAccess('menuSearchPatient')) { ?>
                <li <?php echo $controller == 'patient' && $module == 'reception' && $action == 'viewsearch' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/search_patient.png" width="32" height="32" alt="" />Поиск', array('/reception/patient/viewsearch')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuAddPatient')) { ?>
                <li <?php echo $controller == 'patient' && $module == 'reception' && $action == 'viewadd' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/patient_add.png" width="32" height="32" alt="" />Регистрация', array('/reception/patient/viewadd')) ?>
                </li>
            <? } ?>
            <?php if(Yii::app()->user->checkAccess('menuPatientWrite')) { ?>
                <li <?php echo $controller == 'patient' && $module == 'reception' && ($action == 'writepatientstepone' || $action == 'writepatientsteptwo' || $action == 'writepatientwithoutdata' || $action == 'changeordelete' || ($action == 'writepatientstepone' && isset($_GET['waitingline']) && $_GET['waitingline'] == 1)) && (!isset($_GET['callcenter']) || $_GET['callcenter'] != 1) ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/write_patient.png" width="32" height="32" alt="" />Запись', array('/reception/patient/writepatientstepone')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuPatientRewrite')) { ?>
                <?php
                //var_dump($controller == 'patient' && $module == 'reception' && $action == 'viewrewrite');
               /* var_dump($controller );
                var_dump($module );
                var_dump($action );
                exit();*/
                ?>
                <li <?php echo $controller == 'patient' && $module == 'reception' && $action == 'viewrewrite' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/write_patient.png" width="32" height="32" alt="" />Перезапись', array('/reception/patient/viewrewrite')) ?>
                </li>
            <?php } ?>

            <?php if(Yii::app()->user->checkAccess('menuRaspDoctor')) { ?>
                <li <?php echo $controller == 'shedule' && $module == 'reception' && ($action == 'view') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/shedule.png" width="32" height="32" alt="" />Расписание', array('/reception/shedule/view')) ?>
                </li>
            <?php } ?>

            <?php if(Yii::app()->user->checkAccess('menuReports')) { ?>
                <li <?php echo $controller == 'reports' && $module == 'reception' && ($action == 'view') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/3.3.jpg" width="32" height="32" alt="" />Отчёты', array('#')) ?>

                    <ul class="nav">
                        <?php if(Yii::app()->user->checkAccess('menuReceptionReportsForDay')) { ?>
                            <li <?php echo $module == 'reception' ? 'class="active"' : ''; ?>>
                                <?php echo CHtml::link('Отчёт за день', array('/reception/reports/fordayview')) ?>
                            </li>
                        <?php } ?>
                    </ul>

                </li>
            <?php } ?>

        </ul>
    </li>
<?php } ?>
<?php if(Yii::app()->user->checkAccess('menuCallCenter')) { ?>
    <li <?php echo $module == 'reception' && isset($_GET['callcenter']) && $_GET['callcenter'] == 1 ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/call_center.png" width="32" height="32" alt="" />Call-Центр', array('#')) ?>
        <ul class="nav">
            <?php if(Yii::app()->user->checkAccess('menuPatientWriteCallCenter')) { ?>
                <li <?php echo $controller == 'patient' && $module == 'reception' && (($action == 'writepatientwithoutdata' || $action == 'writepatientstepone' || $action == 'writepatientsteptwo') && isset($_GET['callcenter']) && $_GET['callcenter'] == 1) ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/write_patient.png" width="32" height="32" alt="" />Запись', array('/reception/patient/writepatientwithoutdata?callcenter=1')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuDeleteChangeCallCenter')) { ?>
                <li <?php echo $controller == 'patient' && $module == 'reception' && ($action == 'changeordelete' && isset($_GET['callcenter']) && $_GET['callcenter'] == 1) ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/write_patient.png" width="32" height="32" alt="" />Изменение', array('/reception/patient/changeordelete?callcenter=1')) ?>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
<?php if(Yii::app()->user->checkAccess('menuArm')) { ?>
    <li <?php echo $module == 'doctors' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/doctors_cabinet.png" width="32" height="32" alt="" />Кабинет врача', array('#')) ?>
        <ul class="nav">
            <?php if(Yii::app()->user->checkAccess('menuDoctorMovement')) { ?>
                <li <?php echo $controller == 'shedule' && $module == 'doctors' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/greeting_patient.png" width="32" height="32" alt="" />Приём', array('/doctors/shedule/view')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuPrintMovements')) { ?>
                <li <?php echo $controller == 'print' && $module == 'doctors' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/massprint.png" width="32" height="32" alt="" />Печать', array('/doctors/print/massprintview')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuDoctorEmk')) { ?>
                <li <?php echo $controller == 'patient' && $module == 'doctors' && $action == 'viewsearch' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/view_medcard.png" width="32" height="32" alt="" />Архив приёмов', array('/doctors/patient/viewsearch')) ?>
                </li>
            <?php } ?>
            <!--li <?php echo $controller == 'patient' && $module == 'doctors' && $action == 'viewmonitoring' ? 'class="active"' : ''; ?>>
                <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/view_medcard.png" width="32" height="32" alt="" />Мониторинг', array('/doctors/patient/viewmonitoring')) ?>
            </li-->
        </ul>
    </li>
<?php } ?>
<!--
<li <?php echo $module == 'hospital' ? 'class="active"' : ''; ?>>
	<?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/doctors_cabinet.png" width="32" height="32" alt="" />Стационар', array('#')) ?>
	<ul class="nav">
		<li <?php echo $controller == 'monitoring' && $module == 'hospital' ? 'class="active"' : ''; ?>>
			<?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/greeting_patient.png" width="32" height="32" alt="" />Мониторинг', array('/hospital/monitoring/view')) ?>
		</li>
	</ul>
</li>
-->
<?php if(Yii::app()->user->checkAccess('menuAdmin')) { ?>
    <li <?php echo ($module == 'laboratory') ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/laboratory.png" width="32" height="32" alt="" />Лаборатория', array('#')) ?>
        <ul class="nav">
            <?php if(Yii::app()->user->checkAccess('menuOrgGuides')) { ?>
                <li <?php echo ($controller == 'medcard' && $action == 'view') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Медицинские карты', array('/laboratory/medcard/view')) ?>
                </li>
				<li <?php echo ($controller == 'treatment' && $action == 'view') ? 'class="active"' : ''; ?>>
					<?php echo CHtml::link('Процедурный кабинет', array('/laboratory/treatment/view')) ?>
				</li>
                <li <?php echo ($controller == 'direction' && $action == 'view') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Направления', array('/laboratory/direction/view')) ?>
                </li>
                <li <?php echo ($controller == 'test' && $action == 'view') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Тест', array('/laboratory/test/view')) ?>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
<?php if(Yii::app()->user->checkAccess('menuStat')) { ?>
    <li <?php echo $module == 'statistic' || ($controller == 'tasu' && $module == 'admin') ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/stat.png" width="32" height="32" alt="" />Статистика', array('#')) ?>
        <ul class="nav">
            <?php if(Yii::app()->user->checkAccess('menuTasuIn')) { ?>
                <li <?php echo $controller == 'tasu' && $module == 'admin' && $action == 'viewin' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/3.1.jpg" width="32" height="32" alt="" />Экспорт', array('/admin/tasu/viewin')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuTasuOut')) { ?>
                <li <?php echo ($controller == 'tasu' && $module == 'admin' && $action == 'view') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/3.2.jpg" width="32" height="32" alt="" />Импорт', array('/admin/tasu/view'))
                    //  echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/3.2.jpg" width="32" height="32" alt="" />Загрузка из ТАСУ', array('/admin/tasu2/view'))
                    ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuReport')) { ?>
                <li <?php echo $module == 'statistic' && !($controller == 'history' ||  $controller == 'greetings' || $controller == 'mis') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/icon_sample.png" width="32" height="32" alt="" />Отчётность', array('/statistic/index/view')); ?>
                </li>
            <?php } ?>
			<li <?php echo $module == 'statistic' && ($controller == 'history' ||  $controller == 'greetings' || $controller == 'mis') ? 'class="active"' : ''; ?>>
			<?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/icon_sample.png" width="32" height="32" alt="" />Просмотр приёмов', array('#')); ?>
				<ul class="nav">
					<li <?php echo $module == 'statistic' && $controller == 'history' ? 'class="active"' : ''; ?>>
						<?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/icon_sample.png" width="32" height="32" alt="" />История приёмов', array('/statistic/history/view')); ?>
					</li>
					<li <?php echo $module == 'statistic' && $controller == 'greetings' ? 'class="active"' : ''; ?>>
						<?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/icon_sample.png" width="32" height="32" alt="" />Статистика приёмов', array('/statistic/greetings/view')); ?>
					</li>
					<li <?php echo $module == 'statistic' && $controller == 'mis' ? 'class="active"' : ''; ?>>
						<?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/icon_sample.png" width="32" height="32" alt="" />Статистика МИС', array('/statistic/mis/view')); ?>
					</li>
				</ul>
			</li>
        </ul>
    </li>
<?php } ?>
<?php if(Yii::app()->user->checkAccess('menuAdmin')) { ?>
    <li <?php echo ($module == 'admin' && $controller != 'tasu') || $module == 'guides' || $module == 'settings' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('<img src="'.Yii::app()->getBaseUrl().'/images/icons/admin.png" width="32" height="32" alt="" />Сервис', array('#')) ?>
        <ul class="nav">
            <?php if(Yii::app()->user->checkAccess('menuOrgGuides')) { ?>
            <li <?php echo strstr($module, "guides") || ($module == 'admin' && $action == 'shedulesettings') ? 'class="active"' : ''; ?>>
                <?php echo CHtml::link('Организационные справочники', array('#')) ?>
                <ul class="nav">
                    <?php if(Yii::app()->user->checkAccess('menuGuides')) { ?>
                        <li <?php echo strstr($module, "guides") ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('Справочники', array('/guides/enterprises/view')) ?>
                        </li>
                    <?php } ?>
                    <?php if(Yii::app()->user->checkAccess('menuGreetingRights')) { ?>
                        <li <?php echo $controller == 'modules' && $module == 'admin' && $action == 'shedulesettings' ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('Настройка правил приёма', array('/admin/modules/shedulesettings'));  ?>
                        </li>
                    <?php } ?>
                </ul>
                <?php } ?>
            </li>
            <?php if(Yii::app()->user->checkAccess('menuAdminArm')) { ?>
                <li <?php echo ($controller == 'guides' || $controller == 'diagnosis' || $controller == 'templates' || $controller == 'elements' || $controller == 'categories') && $module == 'admin' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Рабочее место врача', array('#')) ?>
                    <ul class="nav">
                        <?php if(Yii::app()->user->checkAccess('menuGuidesAndTemplates')) { ?>
                            <li <?php echo ($controller == 'guides' || $controller == 'templates' || $controller == 'categories' || $controller == 'guides' || $controller == 'elements') && $module == 'admin' ? 'class="active"' : ''; ?>>
                                <?php echo CHtml::link('Шаблоны и справочники', array('#')) ?>
                                <ul class="nav">
                                    <?php if(Yii::app()->user->checkAccess('menuAdminGuides')) { ?>
                                        <li <?php echo ($controller == 'templates' || $controller == 'categories' || $controller == 'elements' || ($controller == 'guides' && $action != 'allview')) && $module == 'admin' ? 'class="active"' : ''; ?>>
                                            <?php echo CHtml::link('Шаблоны', array('/admin/templates/view')) ?>
                                        </li>
                                    <?php } ?>
                                    <?php if(Yii::app()->user->checkAccess('menuAdminTemplates')) { ?>
                                        <li <?php echo $controller == 'guides' && $module == 'admin' && $action == 'allview' ? 'class="active"' : ''; ?>>
                                            <?php echo CHtml::link('Справочники', array('/admin/guides/allview')) ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if(Yii::app()->user->checkAccess('menuDiagnosis')) { ?>
                            <li <?php echo $controller == 'diagnosis' && $module == 'admin' ? 'class="active"' : ''; ?>>
                                <?php echo CHtml::link('Диагнозы', array('#')); ?>
                                <ul class="nav">
                                    <?php if(Yii::app()->user->checkAccess('menuDiagnosisMkb10')) { ?>
                                        <li <?php echo $controller == 'diagnosis' && $module == 'admin' && $action == 'mkb10view' ? 'class="active"' : ''; ?>>
                                            <?php echo CHtml::link('МКБ-10', array('/admin/diagnosis/mkb10view')) ?>
                                        </li>
                                    <?php } ?>
                                    <?php if(Yii::app()->user->checkAccess('menuDiagnosisLikes')) { ?>
                                        <li <?php echo $controller == 'diagnosis' && $module == 'admin' && $action == 'allview' ? 'class="active"' : ''; ?>>
                                            <?php echo CHtml::link('Любимые диагнозы', array('/admin/diagnosis/allview')) ?>
                                        </li>
                                    <?php } ?>
                                    <?php if(Yii::app()->user->checkAccess('menuDiagnosisRasp')) { ?>
                                        <li <?php echo $controller == 'diagnosis' && $module == 'admin' && $action == 'distribview' ? 'class="active"' : ''; ?>>
                                            <?php echo CHtml::link('Диагнозы для распределения пациентов', array('/admin/diagnosis/distribview')) ?>
                                        </li>
                                    <?php } ?>
                                    <?php if(Yii::app()->user->checkAccess('menuClinicalDiagnosis')) { ?>
                                        <li <?php echo $controller == 'diagnosis' && $module == 'admin' && $action == 'clinicalview' ? 'class="active"' : ''; ?>>
                                            <?php echo CHtml::link('Клинические диагнозы', array('/admin/diagnosis/clinicalview')) ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuAdminUsers')) { ?>
                <li <?php echo (($controller == 'users' || $controller == 'roles') && $module == 'admin') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Пользователи, роли, права', array('/admin/users/view')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuAdminRest')) { ?>
                <li <?php echo ($controller == 'shedule' && $module == 'admin' && $action == 'viewrest') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Настройка календаря', array('/admin/shedule/viewrest')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuAdminDoctorRasp')) { ?>
                <li <?php echo $controller == 'shedule' && $module == 'admin' && $action == 'view' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Настройка расписания врачей', array('/admin/shedule/view')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuUserProfile')) { ?>
                <li <?php echo $controller == 'profile' && $module == 'settings' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Профиль', array('/settings/profile/view')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuSystemSettings')) { ?>
                <li <?php echo $controller == 'system' && $module == 'settings' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Система', array('/settings/system/view')) ?>
                </li>
            <?php } ?>
            <?php if(Yii::app()->user->checkAccess('menuAdminLogs')) { ?>
                <li <?php echo $controller == 'logs' && $module == 'admin' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Логи', array('/admin/logs/view')) ?>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
<!--<li>
    <a href="http://moniiag.toonftp.ru/changelog.txt" class='bold red-color'>Лог изменений</a>
</li>-->
</ul>
</div>