<!-- Шапка -->
<?php echo $enterprise['fullname']; ?><br>
<nobr>Тел.: <?php echo $enterprise['phone']; ?>, адрес: <?php echo $enterprise['address_jur']; ?></nobr>
<div class="header">
    <h3>Пациент <?php echo $greeting['patient_fio']; ?> - Возраст: <?php echo $greeting['full_years']; ?></h3>
</div>
<?php $keysOfTemplates = array_keys($templates);
$templatesIndex = $keysOfTemplates[0];
?>
<!-- Название шаблона (берём первый шаблон)-->
<h4><?php echo $templates[$templatesIndex ]['name']; ?> от <?php echo $greeting['date']; ?> № <?php echo $greeting['card_number']; ?></h4>
<!-- Основной диагноз не выводим -->
<!-- Выводим клинические диагнозы -->
<?php

if ((count($diagnosises['clinicalSecondary'])>0)||   (strlen($diagnosises['noteGreeting'])>0)  )
{
    ?><div><span style="font-size:16px;"><strong>Диагноз</strong></span><?php
    //var_dump($diagnosises);
    //exit();
    if (count($diagnosises['clinicalSecondary'])>0)
    {
        foreach ($diagnosises['clinicalSecondary'] as $oneDiagnosis)
        {
            ?><br><strong> - <?php echo $oneDiagnosis['description']; ?></strong><?php
        }
        if (strlen($diagnosises['noteGreeting'])>0)
        {
            ?><br><strong><?php echo $diagnosises['noteGreeting']; ?></strong><?php
        }
    }
    ?></div><?php
}

/*if (count($diagnosises['clinicalSecondary'])>0)
{
?><div style="margin:0px;"><strong><h3>Диагноз</h3></strong><?php
    foreach ($diagnosises['clinicalSecondary'] as $oneDiagnosis)
    {
        ?><br><strong> - <?php echo $oneDiagnosis['description']; ?></strong><?php
    }
    ?></div><?php
}*/


// Пока я просто оставлю это здесь
/* if (strlen($diagnosises['noteGreeting'])>0)
 {
     ?><div><strong><h3>Клинические диагноз/диагнозы: </h3></strong><?php
     echo $diagnosises['noteGreeting'];
     ?></div><?php
 }*/
// Дальше выводим тело шаблона


foreach ($templates as $oneTemplate)
{

    foreach($oneTemplate['cats']  as $index => $categorie)
    {
        ?>
        <div style="margin-left:20px;">
            <strong style="text-decoration: underline"><?php echo $categorie['element']['name']; ?></strong>
            <p class ="print-elements">
                <?php
                // Вызываем виджет категории
                $printCategorieWidget = CWidget::createWidget('application.modules.doctors.components.widgets.printCategory', array(
                    'categoryToPrint' => $categorie
                ));
                $printCategorieWidget->run();
                ?>
            </p>
        </div>
    <?php
    }
}
?>
<!-- Выведем ФИО врача -->
<strong><span style="font-size:14px;">Врач: <?php echo $greeting['doctor_fio'];  ?></span></strong>