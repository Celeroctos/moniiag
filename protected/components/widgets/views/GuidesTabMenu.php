<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $controller == 'enterprises' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Учреждения', array('/guides/enterprises/view')) ?>
    </li>
    <li <?php echo $controller == 'wards' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Отделения', array('/guides/wards/view')) ?>
    </li>
    <li <?php echo $controller == 'medworkers' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Медработники', array('/guides/medworkers/view')) ?>
    </li>
    <li <?php echo $controller == 'employees' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Сотрудники', array('/guides/employees/view')) ?>
    </li>
    <li <?php echo $controller == 'mkb10' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('МКБ-10', array('/guides/mkb10/view')) ?>
    </li>
</ul>