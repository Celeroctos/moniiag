<div role="complementary" class="bs-sidebar hidden-print affix " >
    <ul class="nav bs-sidenav" id="mainSideMenu">
        <li <?php echo $controller == 'index' && $module == null ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Главная', array('/')) ?>
        </li>
        <li <?php echo $controller == 'index' && $module == 'reception' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Регистратура', array('/reception/index/index')) ?>
            <ul class="nav">
                <li>
                    <li <?php echo $controller == 'patient' && $module == 'reception' ? 'class="active"' : ''; ?>>
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
                <li <?php echo $controller == 'index' && $module == 'reception' && $action == 'writepatientstepone' ? 'class="active"' : ''; ?>>
                    <?php echo CHtml::link('Запись пациента', array('/reception/index/writepatientstepone')) ?>
                </li>
            </ul>
        </li>
        <li <?php echo $module == 'guides' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Справочники', array('/guides/enterprises/view')) ?>
        </li>
        <li>
            <a href="http://moniiag.toonftp.ru/changelog.txt" class='bold red-color'>Лог изменений</a>
        </li>
    </ul>
</div>