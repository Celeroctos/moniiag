<ul class="nav nav-tabs  default-margin-bottom">
    <?php
    if ($this->callcenter)
    {
        ?>

        <li <?php echo $controller == 'patient' && $action == 'writepatientwithoutdata' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Запись опосредованного пациента (без карты)', array('/reception/patient/writepatientwithoutdata'.($this->callcenter ? '?callcenter=1':'' ) )) ?>
        </li>
        <li <?php echo $controller == 'patient' && ($action == 'writepatientstepone' || $action == 'writepatientsteptwo') ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Запись пациента (с картой)', array('/reception/patient/writepatientstepone'.( $this->callcenter ? '?callcenter=1':'' )   )) ?>
        </li>
        <li <?php echo $controller == 'patient' && $action == 'changeordelete' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Изменение / отмена записи', array('/reception/patient/changeordelete'.( $this->callcenter ? '?callcenter=1':'' ))) ?>
        </li>
        <?php
    }
    else
    {
    ?>
        <li <?php echo $controller == 'patient' && ($action == 'writepatientstepone' || $action == 'writepatientsteptwo') ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Запись пациента', array('/reception/patient/writepatientstepone'.( $this->callcenter ? '?callcenter=1':'' )   )) ?>
        </li>
        <li <?php echo $controller == 'patient' && $action == 'writepatientwithoutdata' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Запись опосредованного пациента', array('/reception/patient/writepatientwithoutdata'.($this->callcenter ? '?callcenter=1':'' ) )) ?>
        </li>
    <?php }?>
</ul>