<ul class="nav nav-tabs default-margin-bottom">
    <li <?php echo $action == 'viewregions' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Регионы', array('/guides/cladr/viewregions')) ?>
    </li>
    <li <?php echo $action == 'viewdistricts' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Районы', array('/guides/cladr/viewdistricts')) ?>
    </li>
    <li <?php echo $action == 'viewsettlements' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Населённые пункты', array('/guides/cladr/viewsettlements')) ?>
    </li>
    <li <?php echo $action == 'viewstreets' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Улицы', array('/guides/cladr/viewstreets')) ?>
    </li>
</ul>