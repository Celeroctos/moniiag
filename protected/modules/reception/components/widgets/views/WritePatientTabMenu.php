<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $controller == 'patient' && ($action == 'writepatientstepone' || $action == 'writepatientsteptwo') ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Стандартная запись пациента', array('/reception/patient/writepatientstepone')) ?>
    </li>
    <li <?php echo $controller == 'patient' && $action == 'writepatientwithoutdata' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Запись опосредованного пациента', array('/reception/patient/writepatientwithoutdata')) ?>
    </li>
</ul>