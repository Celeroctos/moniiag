<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $controller == 'enterprises' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Учреждения', array('/guides/enterprises/view')) ?>
    </li>
    <li <?php echo $controller == 'wards' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Отделения', array('/guides/wards/view')) ?>
    </li>
    <li <?php echo $controller == 'medworkers' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Должности', array('/guides/medworkers/view')) ?>
    </li>
    <li <?php echo $controller == 'employees' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Сотрудники', array('/guides/employees/view')) ?>
    </li>
    <li <?php echo $controller == 'contacts' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Контакты', array('/guides/contacts/view')) ?>
    </li>
    <li <?php echo $controller == 'cabinets' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Кабинеты', array('/guides/cabinets/view')) ?>
    </li>
    <li <?php echo $controller == 'privileges' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Льготы', array('/guides/privileges/view')) ?>
    </li>
    <li <?php echo $controller == 'mkb10' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('МКБ-10', array('/guides/mkb10/view')) ?>
    </li>
    <li <?php echo $controller == 'service' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Медуслуги', array('/guides/service/view')) ?>
    </li>
    <li <?php echo $controller == 'insurances' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Страховые компании', array('/guides/insurances/view')) ?>
    </li>
    <li <?php echo $controller == 'cladr' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('КЛАДР', array('/guides/cladr/viewregions')) ?>
    </li>
    <li <?php echo $controller == 'doctype' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Удостоверения личности', array('/guides/doctype/view')) ?>
    </li>
	<li <?php echo $controller == 'medcards' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Номера медкарт', array('/guides/medcards/view')) ?>
    </li>
</ul>