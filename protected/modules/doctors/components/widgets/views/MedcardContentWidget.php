<?php if($currentPatient !== false) { ?>
<script type="text/javascript">
    globalVariables.medcardNumber = '<?php echo $medcard['card_number']; ?>';
    globalVariables.addValueUrl = ''; // ID текущего справочника, в который добавляем значения
<?php if(!$canEditMedcard) { ?>
    $(document).ready(function() {
        $('#primaryDiagnosisChooser .choosed span.glyphicon-remove').remove();
        $('#secondaryDiagnosisChooser .choosed span.glyphicon-remove').remove();
    });
<?php } ?>
</script>
<div class="col-xs-12">
    <div id="accordionX" class="accordion">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a href="#collapseX" data-parent="#accordionX" data-toggle="collapse" class="accordion-toggle"><strong>Реквизитная информация</strong></a>
            </div>
            <div class="accordion-body collapse in" id="collapseX">
                <div class="accordion-inner">
                    <p>
                        ФИО:<strong> <?php echo $medcard['last_name']; ?> <?php echo $medcard['first_name']; ?> <?php echo $medcard['middle_name']; ?></strong><br />
                        Возраст:<strong> <?php echo $medcard['full_years']; ?></strong><br/>
                        Адрес:<strong> <?php echo $medcard['address']; ?></strong><br/>
                        Место работы:<strong> <?php echo $medcard['work_place']; ?>, <?php echo $medcard['work_address']; ?></strong><br/>
                        Телефон:<strong> <?php echo $medcard['contact']; ?></strong><br/>
                        № амбулаторной карты:<strong> <?php echo $medcard['card_number']; ?></strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-xs-12">
    <div id="accordionH" class="accordion">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a href="#collapseH" data-parent="#accordionH" data-toggle="collapse" class="accordion-toggle" data-toggle="tooltip" data-placement="right" title="Здесь Вы можете посмотреть историю изменений медицинской карты. Раскройте список и выберите запись для просмотра изменений медкарты."><strong>История медкарты</strong></a>
            </div>
            <div class="accordion-body collapse in" id="collapseH">
                <div class="accordion-inner">
                    <?php foreach ($historyPoints as $key => $point) { ?>
                   <div>
                       <a href="#<?php echo $point['medcard_id']; ?>" class="medcard-history-showlink"><?php echo $point['change_date']; ?></a>
                   </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>