<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Загрузка из CSV файла', array('/admin/tasu/view')) ?>
    </li>
    <li <?php echo $action == 'viewsync' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Синхронизация с базой ТАСУ', array('/admin/tasu/viewsync')) ?>
    </li>
</ul>