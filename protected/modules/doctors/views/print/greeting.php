<div class="header">
    <h3>Результаты приёма за <?php echo $greeting['date']; ?>, <?php echo $greeting['doctor_fio']; ?> (пациент <?php echo $greeting['patient_fio']; ?>, номер карты <?php echo $greeting['card_number']; ?>)</h3>
</div>
<?php
        foreach ($templates as $oneTemplate)
        {
            // Если у шаблона empty = true, то переходим на следующую итерацию цикла
           // var_dump($oneTemplate['empty']);
            if ($oneTemplate['empty']==true)
                continue;

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
