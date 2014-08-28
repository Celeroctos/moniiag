<!-- Шапка -->
<?php echo $enterprise['fullname']; ?><br>
<nobr>Тел.: <?php echo $enterprise['phone']; ?>, адрес: <?php echo $enterprise['address_jur']; ?>&nbsp;&nbsp;&nbsp;Коллцентр тел.: +7-495-1236013</nobr><br>
<br/><br/>
<h3 style="text-align: center;">ЗАКЛЮЧЕНИЕ ОТ <?php echo $greeting['date']; ?> № <?php echo $greeting['card_number']; ?></h3>
<br/>
<div class="header">
    <span style="font-weight: bold;font-size: 16px;"><?php echo $greeting['patient_fio']; ?>, <?php echo $greeting['full_years'];
        // Вычисляем как просклонять слово "года" для возраста данного пациента
        if ($greeting['full_years'] % 10 == 1)
        {
            echo " год";
        }
        else
        {
            if ( ($greeting['full_years'] % 10>=2) && ($greeting['full_years'] % 10<=4))
            {
                echo " года";
            }
            else
            {
                echo " лет";
            }

        }

        ?></span>
</div>
<?php $keysOfTemplates = array_keys($templates);
$templatesIndex = $keysOfTemplates[0];
?>
<br/>
<?php
//var_dump($diagnosises['noteGreeting']);
//exit();

if ((count($diagnosises['clinicalSecondary'])>0)||   (strlen($diagnosises['noteGreeting'])>0)  )
{
    ?><div><span style="font-size:16px;"><strong>Диагноз </strong></span><?php
    if (strlen($diagnosises['noteGreeting'])>0)
    {

        ?><strong><?php echo $diagnosises['noteGreeting']; ?></strong><?php
    }
    if (count($diagnosises['clinicalSecondary'])>0)
    {
        $wasPrintedDiag = false;
        foreach ($diagnosises['clinicalSecondary'] as $oneDiagnosis)
        {
            ?><strong><br> - <?php echo $oneDiagnosis['description']; ?></strong><?php
        }
    }
    ?></div><?php
}
?><!--br/--><?php
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

// Флаг о том, что была отпечатана первая категория.
//   Сделано для того, чтобы не печатать название первой категории
$wasFirstCategoryPrint = false;
foreach ($templates as $oneTemplate)
{

    foreach($oneTemplate['cats']  as $index => $categorie)
    {
        ?>
        <div style="margin-left:5px; margin-top:0px;">
            <?php
            if ($wasFirstCategoryPrint==true)
            {
                ?>
                <strong style="text-decoration: underline"><?php echo $categorie['element']['name']; ?></strong>
                <?php
            }
            ?>
            <?php $wasFirstCategoryPrint  = true;?>
            <p class ="print-elements">
                <?php
                // Вызываем виджет категории
                $printCategorieWidget = CWidget::createWidget('application.modules.doctors.components.widgets.printCategory', array(
                    'categoryToPrint' => $categorie,
                    'ignoreBrSettings' => true
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
<br/><br/><strong><span style="font-size:14px;">Врач: <?php  echo $greeting['doctor_spec'].' '.$greeting['doctor_fio'];  ?></span></strong>
<!--?php exit();