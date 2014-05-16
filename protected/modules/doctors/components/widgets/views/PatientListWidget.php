<table id="omsSearchWithCardResult" class="table table-condensed table-hover">
    <thead>
    <tr class="header">
        <td>
            ФИО
        </td>
        <td>
            Время приёма
        </td>
        <td>
        </td>
        <!--<td>
        </td>-->
        <td>
        </td>
        <?php if ($currentPatient !== false && Yii::app()->user->checkAccess('canPrintMovement')) { ?>
            <td>
            </td>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($patients as $key => $patient) {
        if ($patient['id'] == $currentSheduleId) { // TODO
            ?>
            <tr class='success activeGreeting'>
        <?php
        } elseif ($patient['is_accepted'] == 1) {
            ?>
            <tr class='orange-block'>
        <?php
        } elseif ($patient['is_beginned'] == 1) {
            ?>
            <tr class='yellow-block'>
        <?php
        } else {
            ?>
            <tr class='magenta-block'>
        <?php } ?>
        <td>
            <?php echo CHtml::link($patient['fio'], array('/doctors/shedule/view?cardid=' . $patient['medcard_id'] . '&date=' . $filterModel->date . '&rowid=' . $patient['id'])); ?>
        </td>
        <td>
            <?php echo $patient['patient_time']; ?>
        </td>
        <td>
            <?php echo CHtml::link('<span class="glyphicon glyphicon-edit"></span>', array('#' . $patient['medcard_id']), array('title' => 'Посмотреть медкарту', 'class' => 'editMedcard')); ?>
        </td>
        <!--<td>
                                <?php echo ($patient['is_beginned'] == 1 || $patient['is_accepted'] == 1) ? '' : CHtml::link('<span class="glyphicon glyphicon-flash"></span>', array('/doctors/shedule/acceptbegin/?id='.$patient['id']), array('title' => 'Начать приём')); ?>
                            </td>-->
        <td>
            <?php
            if (!($patient['is_accepted'] == 1 || $patient['is_beginned'] != 1)) {
                echo CHtml::link('<span class="glyphicon glyphicon-flag"></span>', '#' . $patient['id'],
                    array('title' => 'Закончить приём',
                        'class' => 'accept-greeting-link'));
            }
            ?>
        </td>
        <?php if (Yii::app()->user->checkAccess('canPrintMovement')) { ?>
            <td>
                <?php echo $patient['id'] == $currentSheduleId ? CHtml::link('<span class="glyphicon glyphicon-print"></span>', '#' . $patient['id'],
                    array('title' => 'Печать листа приёма',
                        'class' => 'print-greeting-link')) : ''; ?>
            </td>
        <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
</table>