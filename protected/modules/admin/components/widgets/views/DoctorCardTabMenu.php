<h4>Краткая справка</h4>
<p>Раздел предназначен для редактирования содержания медицинской карты для рабочего места врача. Карта у врача разбита на категории (раскрывающиеся списки), внутри них имеются управляющие элементы, которые могут представлять собой, в том числе, выбор значения из справочника.
    При формировании шаблона карты требуется определить группы, поля карты, справочники и привязать последние к определённым полям. Справочники при необходимости можно дополнять значениями.
</p>

<ul class="nav nav-tabs  default-margin-bottom">
    <li <?php echo $controller == 'templates' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Шаблоны', array('/admin/templates/view')) ?>
    </li>
    <li <?php echo $controller == 'categories' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Категории', array('/admin/categories/view')) ?>
    </li>
    <li <?php echo $controller == 'guides' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Справочники', array('/admin/guides/view')) ?>
    </li>
    <li <?php echo $controller == 'elements' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Элементы управления', array('/admin/elements/view')) ?>
    </li>
</ul>