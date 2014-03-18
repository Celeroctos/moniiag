    <div class="form-group">
            <label for="searchValue" class="col-xs-1" control-label">Поиск</label>
            <div class =  "col-xs-3">
                <input type="text" class="form-control" id="searchValue" placeholder="Введите для поиска">
            </div>
    </div>
    <div>
    <ul class="nav nav-tabs  default-margin-bottom medguide-list">
    <?php foreach($tabs as $index => $tab) { ?>
        <li <?php echo $current == $tab['id'] ? 'class="active"' : 'class="no-display medguide-item"'; ?>>
            <?php echo CHtml::link($tab['name'], array('/admin/guides/allview?guideid='.$tab['id'])) ?>
        </li>
    <?php }  ?>
    </ul>
    </div>