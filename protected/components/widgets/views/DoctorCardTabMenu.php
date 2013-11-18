<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $controller == 'categories' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Категории', array('/admin/categories/view')) ?>
    </li>
    <li <?php echo $controller == 'guides' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Справочники', array('/admin/guides/view')) ?>
    </li>
    <li <?php echo $controller == 'elements' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Элементы управления', array('/admin/elements/view')) ?>
    </li>
</ul>