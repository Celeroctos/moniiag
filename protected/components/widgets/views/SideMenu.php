<div role="complementary" class="bs-sidebar hidden-print" >
    <ul class="nav bs-sidenav" id="mainSideMenu">
        <li <?php echo $controller == 'index' && $module == null ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Главная', array('/')) ?>
        </li>
        <li <?php echo $controller == 'index' && $module == 'reception' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Регистратура', array('/reception/index/index')) ?>
            <ul class="nav">
                <li>
                    <li <?php echo $controller == 'patient' && $module == 'reception' && ($action == 'viewsearch' || $action == 'viewadd' || $action == 'addpregnant' || $action == 'index') ? 'class="active"' : ''; ?>>
                        <?php echo CHtml::link('Работа с пациентами', array('/reception/patient/index')) ?>
                    </li>
                    <ul class="nav">
                        <li <?php echo $controller == 'patient' && $module == 'reception' && $action == 'viewsearch' ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('Поиск пациента', array('/reception/patient/viewsearch')) ?>
                        </li>
                        <li <?php echo $controller == 'patient' && $module == 'reception' && $action == 'viewadd' ? 'class="active"' : ''; ?>>
                            <?php echo CHtml::link('Добавление пациента', array('/reception/patient/viewadd')) ?>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" >Расписание врачей</a>
                    <ul class="nav">
                        <li>
                            <a href="#" >Сводное</a>
                        </li>
                        <li>
                            <a href="#" >По врачам</a>
                        </li>
                    </ul>
                </li>
                <li <?php echo $controller == 'patient' && $module == 'reception' && ($action == 'writepatientstepone' || $action == 'writepatientsteptwo') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Запись пациента', array('/reception/patient/writepatientstepone')) ?>
                </li>
            </ul>
        </li>
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
        <li <?php echo $module == 'guides' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Справочники', array('/guides/enterprises/view')) ?>
        </li>
        <li <?php echo $module == 'settings' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Настройки', array('/settings/index/view')) ?>
            <ul class="nav">
                <li <?php echo $controller == 'profile' && $module == 'settings' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Профиль', array('/settings/profile/view')) ?>
                </li>
                <li <?php echo $controller == 'system' && $module == 'settings' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Система', array('/settings/system/view')) ?>
                </li>
            </ul>
        </li>
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
                    </ul>
                </li>
                <li <?php echo ($controller == 'shedule' && $module == 'admin') ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Настройка расписания', array('/admin/shedule/view')) ?>
                </li>
            </ul>
        </li>
        <li>
            <a href="http://moniiag.toonftp.ru/changelog.txt" class='bold red-color'>Лог изменений</a>
        </li>
    </ul>
</div>