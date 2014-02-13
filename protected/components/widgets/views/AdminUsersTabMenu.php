<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $controller == 'users' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Пользователи', array('/admin/users/view')) ?>
    </li>
    <li <?php echo $controller == 'roles' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Роли', array('/admin/roles/view')) ?>
    </li>
    <li <?php echo $controller == 'roles' && $action == 'startpagesview' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Стартовые страницы', array('/admin/roles/startpagesview')) ?>
    </li>
</ul>