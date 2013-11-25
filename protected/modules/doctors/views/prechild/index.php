<div class="row">
    <div class="col-xs-5">

    </div>
    <?php if($currentPatient !== false) { ?>
        <div class="col-xs-7">
            <?php $this->widget('application.modules.doctors.components.widgets.CategorieViewWidget',array(
                'currentPatient' => $currentPatient,
                'templateType' => 1 // Шаблон ведения беременных
            )); ?>
        </div>
    <?php } ?>
</div>