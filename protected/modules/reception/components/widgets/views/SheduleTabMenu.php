<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $controller == 'shedule' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Расписание врачей', array('/reception/shedule/view')) ?>
    </li>
    <li <?php echo $controller == 'shedule' && $action == 'cardview' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Разнос карт', array('/reception/shedule/cardview')) ?>
    </li>
</ul>