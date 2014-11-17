<!-- Старый код-->
<!--div class="header">
    <h3>Результаты приёма за <?php echo $greeting['date']; ?>, <?php echo $greeting['doctor_fio']; ?> (пациент <?php echo $greeting['patient_fio']; ?>, номер карты <?php echo $greeting['card_number']; ?>)</h3>
</div-->

<div class="header">
    <h3>
        <nobr><?php echo $greeting['date']; ?>, <?php echo $greeting['patient_initials']; ?>, <?php echo $greeting['full_years']; ?> лет, <?php echo $greeting['card_number']; ?></nobr> 
    <br/>
        <nobr><?php echo $greeting['doctor_spec']; ?> <?php echo $greeting['doctor_initials']; ?></nobr>
    </h3>
</div>
<?php
        // Сначала печатаем шаблоны, у которых template_page_id = 0
        // Потом выводим диагнозы
        // Потом выводим шаблоны, у которых template_page_id = 1
        foreach ($templates as $oneTemplate)
        {
            // Если у шаблона empty = true, то переходим на следующую итерацию цикла
            // var_dump($oneTemplate['empty']);
            if ($oneTemplate['empty']==true)
                continue;

            // Если шаблон не основной (а рекомендации)
            if ($oneTemplate['template_page_id']!=0)
            {
                continue;
            }
            // Печатаем название шаблона
            ?><span class="templateNamePrinting"><?php echo $oneTemplate['name']; ?></span><?php
            foreach($oneTemplate['cats']  as $index => $categorie) {
                // Печатаем название категории
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
        // Выводим диагнозы
        if ((count($diagnosises['clinicalSecondary'])>0) || (strlen($diagnosises['noteGreeting'])>0) )
        {
            ?><div><strong><h3>Клинический диагноз: </h3></strong><?php
        }

        if (strlen($diagnosises['noteGreeting'])>0)
        {
            echo $diagnosises['noteGreeting'];
        }
        if (count($diagnosises['clinicalSecondary'])>0)
        {
            foreach ($diagnosises['clinicalSecondary'] as $oneDiagnosis)
            {
                ?><br> - <?php
                echo $oneDiagnosis['description'];
            }
        }


            if ((count($diagnosises['clinicalSecondary'])>0) || (strlen($diagnosises['noteGreeting'])>0) )
            {
                ?><div><?php
            }


        if (count($diagnosises['primary'])>0)
        {
            ?><div><strong><h3>Основной диагноз по МКБ-10: </h3></strong><?php
            foreach ($diagnosises['primary'] as $oneDiagnosis)
            {
                echo $oneDiagnosis['description'];
            }
            ?></div><?php
        }
        if (count($diagnosises['complicating'])>0)
        {
            ?><div><strong><h3>Осложнения основного диагноза по МКБ-10: </h3></strong><?php
            foreach ($diagnosises['complicating'] as $oneDiagnosis)
            {
                ?><br> - <?php
                echo $oneDiagnosis['description'];
            }
            ?></div><?php
        }
        if (count($diagnosises['secondary'])>0)
        {
            ?><div><strong><h3>Сопутствующие диагнозы по МКБ-10: </h3></strong><?php
            foreach ($diagnosises['secondary'] as $oneDiagnosis)
            {
                ?><br> - <?php
                echo $oneDiagnosis['description'];
            }
            ?></div><?php
        }


        foreach ($templates as $oneTemplate)
        {
            // Если у шаблона empty = true, то переходим на следующую итерацию цикла
           // var_dump($oneTemplate['empty']);
            if ($oneTemplate['empty']==true)
                continue;
            // Если шаблон не рекомендации(а основной, например)
            if ($oneTemplate['template_page_id']!=1)
            {
                continue;
            }

            // Печатаем название шаблона
            ?><h3><?php echo $oneTemplate['name']; ?></h3><?php
            foreach($oneTemplate['cats']  as $index => $categorie) {
                // Печатаем название категории
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
