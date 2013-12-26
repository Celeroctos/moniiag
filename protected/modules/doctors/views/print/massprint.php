<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<h3>Массовая печать приёмов для медицинских карт</h3>
<p class="text-left">
    С помощью элементов управления, расположенных ниже, Вы можете выбрать врачей, для которых необходимо напечатать определённые записи медицинской карты, а также сами записи.
</p>
<form class="form-horizontal col-xs-9" role="form" id="doctor-search-form" method="post">
    <div class="form-group chooser" id="doctorChooser">
        <label for="doctor" class="col-xs-2 control-label">Врач (начинайте вводить текст в поле)</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" autofocus id="doctor" placeholder="ФИО врача">
            <ul class="variants no-display">
            </ul>
            <div class="choosed">
            </div>
        </div>
    </div>
</form>