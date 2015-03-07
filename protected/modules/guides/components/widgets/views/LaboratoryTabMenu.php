<ul class="nav nav-tabs default-margin-bottom">
    <li <?php echo $controller == 'analysisparam' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Параметры анализов', array('/guides/laboratory/analysisparam')) ?>
    </li>
    <li <?php echo $controller == 'analysistype' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Типы анализов', array('/guides/laboratory/analysistype')) ?>
    </li>
    <li <?php echo $controller == 'analysistypetemplate' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Шаблоны анализов', array('/guides/laboratory/analysistypetemplate')) ?>
    </li>
    <li <?php echo $controller == 'analyzertype' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Типы анализаторов', array('/guides/laboratory/analyzertype')) ?>
    </li>
    <li <?php echo $controller == 'analyzertypeanalysis' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Анализы доступные для анализаторов', array('/guides/laboratory/analyzertypeanalysis')) ?>
    </li>
    <li <?php echo $controller == 'analysissampletype' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Типы образцов для анализов', array('/guides/laboratory/analysissampletype')) ?>
    </li>
</ul>