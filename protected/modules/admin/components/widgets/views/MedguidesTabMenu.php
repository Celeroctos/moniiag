<?php if($current != -1) { ?>
    <ul class="nav nav-tabs  default-margin-bottom">
    <?php foreach($tabs as $index => $tab) { ?>
        <li <?php echo $current == $tab['id'] ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link($tab['name'], array('/admin/guides/allview?guideid='.$tab['id'])) ?>
        </li>
    <?php } ?>
    </ul>
<?php } else { ?>
    <p>Выбранный справочник недоступен.</p>
<?php } ?>