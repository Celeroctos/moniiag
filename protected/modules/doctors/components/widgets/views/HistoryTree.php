<script type="text/javascript">
    elementsDependences = [];
    $(document).ready(function() {
        // Выяснение зависимостей
        var deps = elementsDependences;
        for(var i = 0; i < deps.length; i++) {
            var elementValue = $('select[id$="_' + deps[i].elementId + '"]').val();
            if(deps[i].dependences.list.length > 0) {
                filteredDeps.push(deps[i]);
                changeControlState(deps[i], elementValue);
            }
        }

        function changeControlState(dep, elementValue) {
            for(var j = 0; j < dep.dependences.list.length; j++) {
                if(dep.dependences.list[j].value == elementValue) {
                    if(dep.dependences.list[j].action == 1) { // Это "скрыть"
                        $('[id$="_' + dep.dependences.list[j].elementId + '"]').parents('.form-group').hide();
                    } else if(deps[i].dependences.list[j].action == 2) { // Это "показать"
                        $('[id$="_' + dep.dependences.list[j].elementId + '"]').parents('.form-group').show();
                    }
                }  else {
                    // Противоположное действие экшену по дефолту
                    if(dep.dependences.list[j].action == 1) { // Это "скрыть"
                        $('[id$="_' + dep.dependences.list[j].elementId + '"]').parents('.form-group').show();
                    } else if(dep.dependences.list[j].action == 2) { // Это "показать"
                        $('[id$="_' + dep.dependences.list[j].elementId + '"]').parents('.form-group').hide();
                    }
                }
            }
        }
    });
</script>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'patient-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/doctors/shedule/editpatient'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
));
foreach($dividedCats as $key => $template) {
    ?>
    <h4>
        <?php echo $template['name']; ?>
    </h4>
    <?php
    foreach($template['cats']  as $index => $categorie) {
        $this->drawHistoryCategorie($categorie, $index, $form, $model, 'h'.$key, $key, $lettersInPixel);
    }
}
$this->endWidget();
?>