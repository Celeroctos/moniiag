<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $controller == 'tasu' && $action == 'viewin' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Выгрузка приёмов', array('/admin/tasu/viewin')) ?>
    </li>
    <li <?php echo $controller == 'tasu' && $action == 'viewmedcards' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Выгрузка медкарт', array('/admin/tasu/viewmedcards')) ?>
    </li>
</ul>