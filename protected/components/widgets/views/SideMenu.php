<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/sidemenu.js"></script>
<style>
    #mainSideMenu li li {
        font-size: <?php echo isset(Yii::app()->user->fontSize) ? Yii::app()->user->fontSize - 1 : 11; ?>px !important;
    }
</style>
<div role="complementary" class="bs-sidebar hidden-print" >
    <ul class="nav bs-sidenav" id="mainSideMenu">
        <!--<li <?php echo $controller == 'index' && $module == null ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('<img src="/images/icons/icon_sample.png" width="32" height="32" alt="" />Главная', array('/')) ?>
        </li>-->
        <?php if(Yii::app()->user->checkAccess('menuRegister')) { ?>
        <li <?php echo $module == 'reception' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('<img src="/images/icons/register.png" width="32" height="32" alt="" />Регистратура', array('#')) ?>
            <ul class="nav">
                <?php if(Yii::app()->user->checkAccess('menuSearchPatient')) { ?>
                    <li <?php echo $controller == 'patient' && $module == 'reception' && $action == 'viewsearch' ? 'class="active"' : ''; ?>>
                        <?php echo CHtml::link('<img src="/images/icons/search_patient.png" width="32" height="32" alt="" />Поиск пациента', array('/reception/patient/viewsearch')) ?>
                    </li>
                <?php } ?>
                <?php if(Yii::app()->user->checkAccess('menuAddPatient')) { ?>
                    <li <?php echo $controller == 'patient' && $module == 'reception' && $action == 'viewadd' ? 'class="active"' : ''; ?>>
                        <?php echo CHtml::link('<img src="/images/icons/patient_add.png" width="32" height="32" alt="" />Регистрация пациента', array('/reception/patient/viewadd')) ?>
                    </li>
                <? } ?>
                <?php if(Yii::app()->user->checkAccess('menuPatientWrite')) { ?>
                    <li <?php echo $controller == 'patient' && $module == 'reception' && ($action == 'writepatientstepone' || $action == 'writepatientsteptwo') ? 'class="active"' : ''; ?>>
                        <?php echo CHtml::link('<img src="/images/icons/write_patient.png" width="32" height="32" alt="" />Запись пациента', array('/reception/patient/writepatientstepone')) ?>
                    </li>
                <?php } ?>
                <?php if(Yii::app()->user->checkAccess('menuRaspDoctor')) { ?>
                <li <?php echo $controller == 'shedule' && $module == 'reception' && ($action == 'view') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="/images/icons/shedule.png" width="32" height="32" alt="" />Расписание', array('/reception/shedule/view')) ?>
                </li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>


        <?php if(Yii::app()->user->checkAccess('menuCallCenter')) { ?>
            <li <?php echo $module == 'reception' ? 'class="active"' : ''; ?>>
                <?php echo CHtml::link('<img src="/images/icons/call_center.png" width="32" height="32" alt="" />Call-Центр', array('#')) ?>
                <ul class="nav">
                    <?php if(Yii::app()->user->checkAccess('menuPatientWriteCallCenter')) { ?>
                        <li <?php echo $controller == 'patient' && $module == 'reception' && ($action == 'writepatientstepone' || $action == 'writepatientsteptwo') ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('<img src="/images/icons/write_patient.png" width="32" height="32" alt="" />Запись пациента', array('/reception/patient/writepatientwithoutdata?callcenter=1')) ?>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>


        <?php if(Yii::app()->user->checkAccess('menuArm')) { ?>
        <li <?php echo $module == 'doctors' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('<img src="/images/icons/doctors_cabinet.png" width="32" height="32" alt="" />Кабинет врача', array('#')) ?>
            <ul class="nav">
                <?php if(Yii::app()->user->checkAccess('menuDoctorMovement')) { ?>
                <li <?php echo $controller == 'shedule' && $module == 'doctors' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="/images/icons/greeting_patient.png" width="32" height="32" alt="" />Приём пациентов', array('/doctors/shedule/view')) ?>
                </li>
                <?php } ?>
                <?php if(Yii::app()->user->checkAccess('menuPrintMovements')) { ?>
                <li <?php echo $controller == 'print' && $module == 'doctors' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="/images/icons/massprint.png" width="32" height="32" alt="" />Массовая печать', array('/doctors/print/massprintview')) ?>
                </li>
                <?php } ?>
                <?php if(Yii::app()->user->checkAccess('menuDoctorEmk')) { ?>
                <li <?php echo $controller == 'patient' && $module == 'doctors' && $action == 'viewsearch' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="/images/icons/view_medcard.png" width="32" height="32" alt="" />Просмотр ЭМК', array('/doctors/patient/viewsearch')) ?>
                </li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
        <?php if(Yii::app()->user->checkAccess('menuStat')) { ?>
        <li <?php echo $module == 'statistic' || ($controller == 'tasu' && $module == 'admin') ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('<img src="/images/icons/stat.png" width="32" height="32" alt="" />Статистика', array('#')) ?>
            <ul class="nav">
                <?php if(Yii::app()->user->checkAccess('menuTasuIn')) { ?>
                <li <?php echo $controller == 'tasu' && $module == 'admin' && $action == 'viewin' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="/images/icons/3.1.jpg" width="32" height="32" alt="" />Загрузка в ТАСУ', array('/admin/tasu/viewin')) ?>
                </li>
                <?php } ?>
                <?php if(Yii::app()->user->checkAccess('menuTasuOut')) { ?>
                <li <?php echo ($controller == 'tasu' && $module == 'admin' && $action == 'view') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="/images/icons/3.2.jpg" width="32" height="32" alt="" />Загрузка из ТАСУ', array('/admin/tasu/view'))
                  //  echo CHtml::link('<img src="/images/icons/3.2.jpg" width="32" height="32" alt="" />Загрузка из ТАСУ', array('/admin/tasu2/view'))
                    ?>
                </li>
                <?php } ?>
                <?php if(Yii::app()->user->checkAccess('menuReport')) { ?>
                <li <?php echo $module == 'statistic' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('<img src="/images/icons/icon_sample.png" width="32" height="32" alt="" />Отчётность', array('/statistic/index/view')); ?>
                </li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
        <?php if(Yii::app()->user->checkAccess('menuAdmin')) { ?>
        <li <?php echo ($module == 'admin' && $controller != 'tasu') || $module == 'guides' || $module == 'settings' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('<img src="/images/icons/admin.png" width="32" height="32" alt="" />Администрирование', array('#')) ?>
            <ul class="nav">
                <?php if(Yii::app()->user->checkAccess('menuOrgGuides')) { ?>
                <li <?php echo $module == 'guides' || ($module == 'admin' && $action == 'shedulesettings') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Организационные справочники', array('#')) ?>
                    <ul class="nav">
                        <?php if(Yii::app()->user->checkAccess('menuGuides')) { ?>
                        <li <?php echo $module == 'guides' ? 'class="active"' : ''; ?>>
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
                <li <?php echo $controller == 'shedule' && $module == 'admin' ? 'class="active"' : ''; ?>>
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