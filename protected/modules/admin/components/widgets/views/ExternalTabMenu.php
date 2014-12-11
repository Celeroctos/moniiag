<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $controller == 'external' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Ключи', array('/admin/external/view')) ?>
    </li>
    <li <?php echo $controller == 'rules' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Правила', array('/admin/rules/view')) ?>
    </li>
</ul>