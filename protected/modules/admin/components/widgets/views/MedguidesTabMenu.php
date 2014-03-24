    <div class="form-group">
            <label for="searchValue" class="col-xs-1" control-label">Поиск</label>
            <div class =  "col-xs-3">
                <input type="text" class="form-control" id="searchValue" placeholder="Введите для поиска">
            </div>
    </div>
    <div class="row tab-list">
        <ul class="nav nav-tabs  default-margin-bottom medguide-list">
        <?php foreach($tabs as $index => $tab) {
            if ($current == $tab['id']) {?>
            <li class="active">
                <?php echo CHtml::link($tab['name'], array('/admin/guides/allview?guideid='.$tab['id'])) ?>
            </li>
            <?php } ?>
        <?php }  ?>
        <?php foreach($tabs as $index => $tab) {
            if ($current != $tab['id']) {?>
            <li class="medguide-item">
                <?php echo CHtml::link($tab['name'], array('/admin/guides/allview?guideid='.$tab['id'])) ?>
            </li>
            <?php } ?>
        <?php }  ?>
        </ul>
    </div>