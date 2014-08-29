<script type="text/javascript">
elementsDependences = [];
filteredDeps = [];
</script>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'patient-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/doctors/shedule/editpatient'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
));
// Выводим список диагнозов
if (count($primaryDiagnosis)>0)
{
	?><h4>Диагноз:</h4><ul><?php
	foreach ($primaryDiagnosis as $diag)
	{
		?><li><?php
			echo $diag['description'];
		?></li><?php
	}
	?></ul><?php
}

// Осложнения:
if (count($complicating)>0)
{
    ?><h4>Осложнения основного диагноза по МКБ-10:</h4><ul><?php
    foreach ($complicating as $diag)
    {
        ?><li><?php
        echo $diag['description'];
        ?></li><?php
    }
    ?></ul><?php
}

// Клинические диагнозы
if (count($clinicalPrimaryDiagnosis)>0)
{
    ?><h4>Клинический диагноз:</h4><ul><?php
	foreach ($clinicalPrimaryDiagnosis as $diag)
	{
		?><li><?php
		echo $diag['description'];
		?></li><?php
	}
	?></ul><?php
}

if (count($secondaryDiagnosis)>0)
{
	?><h4>Сопутствующие диагнозы</h4><ul><?php
	foreach ($secondaryDiagnosis as $diag)
	{
		?><li><?php
			echo $diag['description'];
		?></li><?php
	}
	?></ul><?php
	
}

// Сопутсвующие клинические диагнозы
if (count($clinicalSecondaryDiagnosis)>0)
{
?><h4>Клинические диагнозы</h4><ul><?php
	foreach ($clinicalSecondaryDiagnosis as $diag)
	{
		?><li><?php
		echo $diag['description'];
		?></li><?php
	}
	?></ul><?php
	
}

// Выводим примечания (клинические диагнозы)
if ($noteDiagnosis!='')
{
    ?><h4>Клинические диагнозы</h4><?php
        echo $noteDiagnosis;
    ?><?php
}

foreach($dividedCats as $key => $template) {
    ?><h4><?php echo $template['name']; ?></h4><?php
    foreach($template['cats']  as $index => $categorie) {
        $this->drawHistoryCategorie($categorie, $index, $form, $model, 'h'.$key, $key, $lettersInPixel);
    }
}
$this->endWidget();
?>
<script type="text/javascript">
    /*
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
    });*/
</script>