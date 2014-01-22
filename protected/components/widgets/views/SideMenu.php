<div role="complementary" class="bs-sidebar hidden-print" >
    <ul class="nav bs-sidenav" id="mainSideMenu">
        <li <?php echo $controller == 'index' && $module == null ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Главная', array('/')) ?>
        </li>
        <?php if(Yii::app()->user->checkAccess('menuRegister')) { ?>
        <li <?php echo $controller == 'index' && $module == 'reception' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Регистратура', array('/reception/index/index')) ?>
            <ul class="nav">
                <li>
                    <?php if(Yii::app()->user->checkAccess('menuWorkWithPatients')) { ?>
                    <li <?php echo $controller == 'patient' && $module == 'reception' && ($action == 'viewsearch' || $action == 'viewadd' || $action == 'addpregnant' || $action == 'index') ? 'class="active"' : ''; ?>>
                        <?php echo CHtml::link('Работа с пациентами', array('/reception/patient/index')) ?>
                    </li>
                    <ul class="nav">
                        <?php if(Yii::app()->user->checkAccess('menuSearchPatient')) { ?>
                        <li <?php echo $controller == 'patient' && $module == 'reception' && $action == 'viewsearch' ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('Поиск пациента', array('/reception/patient/viewsearch')) ?>
                        </li>
                        <?php } ?>
                        <?php if(Yii::app()->user->checkAccess('menuAddPatient')) { ?>
                        <li <?php echo $controller == 'patient' && $module == 'reception' && $action == 'viewadd' ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('Добавление пациента', array('/reception/patient/viewadd')) ?>
                        </li>
                        <? } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php if(Yii::app()->user->checkAccess('menuSearchPatient')) { ?>
                <li>
                    <?php if(Yii::app()->user->checkAccess('menuRaspDoctor')) { ?>
                    <a href="#" >Расписание врачей</a>
                    <ul class="nav">
                        <?php if(Yii::app()->user->checkAccess('menuRaspDoctorSvod')) { ?>
                        <li>
                            <a href="#" >Сводное</a>
                        </li>
                        <?php } ?>
                        <?php if(Yii::app()->user->checkAccess('menuRaspDoctorDoc')) { ?>
                        <li>
                            <a href="#" >По врачам</a>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php } ?>
                <?php if(Yii::app()->user->checkAccess('menuPatientWrite')) { ?>
                <li <?php echo $controller == 'patient' && $module == 'reception' && ($action == 'writepatientstepone' || $action == 'writepatientsteptwo') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Запись пациента', array('/reception/patient/writepatientstepone')) ?>
                </li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
        <?php if(Yii::app()->user->checkAccess('menuArm')) { ?>
        <li <?php echo $module == 'doctors' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('АРМ врача', array('/doctors/index/view')) ?>
            <ul class="nav">
                <li <?php echo $controller == 'shedule' && $module == 'doctors' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Приём больных', array('/doctors/shedule/view')) ?>
                </li>
                <li <?php echo $controller == 'print' && $module == 'doctors' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Печать приёмов', array('/doctors/print/massprintview')) ?>
                </li>
            </ul>
        </li>
        <?php } ?>
        <?php if(Yii::app()->user->checkAccess('menuGuides')) { ?>
        <li <?php echo $module == 'guides' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Справочники', array('/guides/enterprises/view')) ?>
        </li>
        <?php } ?>
        <?php if(Yii::app()->user->checkAccess('menuSettings')) { ?>
        <li <?php echo $module == 'settings' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Настройки', array('/settings/index/view')) ?>
            <ul class="nav">
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
            </ul>
        </li>
        <?php } ?>
        <?php if(Yii::app()->user->checkAccess('menuAdmin')) { ?>
        <li <?php echo $module == 'admin' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Администрирование', array('/admin/index/index')) ?>
            <ul class="nav">
                <li <?php echo (($controller == 'users' || $controller == 'roles') && $module == 'admin') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Пользователи', array('/admin/users/view')) ?>
                </li>
                <li <?php echo ($controller == 'modules') && $module == 'admin' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Настройки модулей', array('/admin/modules/infoview')) ?>
                    <ul class="nav">
                        <li <?php echo $controller == 'modules' && $module == 'admin' && $action == 'shedulesettings' ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('Расписание врачей', array('/admin/modules/shedulesettings'));  ?>
                        </li>
                    </ul>
                </li>
                <li <?php echo ($controller == 'templates' || $controller == 'categories' || $controller == 'guides' || $controller == 'elements') && $module == 'admin' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Рабочее место врача', array('/admin/templates/view')) ?>
                    <ul class="nav">
                        <li <?php echo $controller == 'guides' && $module == 'admin' && $action == 'allview' ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('Врачебные справочники', array('/admin/guides/allview')) ?>
                        </li>
                        <li <?php echo $controller == 'diagnosis' && $module == 'admin' && $action == 'allview' ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('Любимые диагнозы', array('/admin/diagnosis/allview')) ?>
                        </li>
                        <li <?php echo $controller == 'diagnosis' && $module == 'admin' && $action == 'distribview' ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('Диагнозы для распределения больных', array('/admin/diagnosis/distribview')) ?>
                        </li>
                    </ul>
                </li>
                <li <?php echo ($controller == 'shedule' && $module == 'admin') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Настройка расписания', array('/admin/shedule/view')) ?>
                </li>
                <li <?php echo ($controller == 'tasu' && $module == 'admin') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('ТАСУ', array('/admin/tasu/view')) ?>
                </li>
            </ul>
        </li>
        <?php } ?>
        <li>
            <a href="http://moniiag.toonftp.ru/changelog.txt" class='bold red-color'>Лог изменений</a>
        </li>
    </ul>
</div>