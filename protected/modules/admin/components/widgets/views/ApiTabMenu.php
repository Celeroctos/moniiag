<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $controller == 'api' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Ключи', array('/admin/api/view')) ?>
    </li>
    <li <?php echo $controller == 'api' && $action == 'rule' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Правила', array('/admin/api/rule')) ?>
    </li>
</ul>