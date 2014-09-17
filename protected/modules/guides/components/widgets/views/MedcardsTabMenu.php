<ul class="nav nav-tabs default-margin-bottom">
    <li <?php echo $action == 'viewprefixes' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Префиксы', array('/guides/medcards/viewprefixes')) ?>
    </li>
    <li <?php echo $action == 'viewpostfixes' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Постфиксы', array('/guides/medcards/viewpostfixes')) ?>
    </li>
    <li <?php echo $action == 'viewrules' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Правила', array('/guides/medcards/viewrules')) ?>
    </li>
</ul>