<div class="row">
    <div class="col-xs-5">
        <div class="row">
            <h5><strong>Список беременных пациенток для наблюдения</strong></h5>
            <div class="col-xs-12 borderedBox">
                <table id="omsSearchWithCardResult" class="table table-condensed table-hover">
                    <thead>
                    <tr class="header">
                        <td>
                            ФИО
                        </td>
                        <td>
                            Посмотреть карту
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($patients as $key => $patient) { ?>
                        <tr <?php echo $patient['card_id'] == $currentPatient ? "class='success'" : ''; ?>>
                            <td>
                                <?php echo CHtml::link($patient['fio'], array('/doctors/prechild/view?cardid='.$patient['card_id'])); ?>
                            </td>
                            <td>
                                <?php echo CHtml::link('<span class="glyphicon glyphicon-edit"></span>', array('/reception/patient/editcardview/?cardid='.$patient['card_id'])); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
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