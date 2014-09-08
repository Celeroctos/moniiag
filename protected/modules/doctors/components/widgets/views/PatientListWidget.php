<?php
    // Вычислим, можно ли менять у приёма статус
    $enableChangeStatus = false;
    // Если у patientDay такой же месяц и год, как у текущей даты - поднимаем флаг

    $monthOfGreeting = date('m',strtotime($patientsDay));
    $yearOfGreeting = date('Y',strtotime($patientsDay));
    $currentMonth = date('m');
    $currentYear = date('Y');
if (($monthOfGreeting ==$currentMonth)&&($yearOfGreeting ==$currentYear))
    {
        $enableChangeStatus = true;
    }

    $disabledAttr = "";
    if (!$enableChangeStatus)
        $disabledAttr = "disabled";

//var_dump($tableId);
?>
<table id="<?php echo $tableId; ?>" class="table table-condensed table-hover">
    <thead>
    <tr class="header">
        <td>
            ФИО
        </td>
        <?php if(!$isWaitingLine) { ?>
        <td>
            Время приёма
        </td>
        <?php } ?>
        <td>
            Статус приёма
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
        // Если нет пацента - выводим пустую строку со временем
        if ($patient['id']==null)
        {
            // Выводим только время приёма
            ?>
            <tr class="emptySheduleItem no-display">
                <td>
                </td>
                <td>
                    <?php echo $patient['timeBegin']; ?>
                </td>
                <td colspan="3">
                </td>
            </tr>
            <?php
            continue;
        }
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
            <?php 
			if($patient['medcard_id'] != null) {
				echo CHtml::link($patient['fio'], array('/doctors/shedule/view?cardid=' . $patient['medcard_id'] . '&date=' . $filterModel->date . '&rowid=' . $patient['id'])); 
			} else {
				echo $patient['fio'];
			}
			?>
        </td>
        <?php if(!$isWaitingLine) { ?>
        <td>
            <?php echo $patient['patient_time']; ?>
        </td>
        <?php } ?>

        <td class="greetingStatusCell">
            <div id="status-container<?php echo $patient['id'];?>">
            <input type="radio" id="greetingStatus<?php echo $patient['id'];?>"
                   <?php echo $disabledAttr; ?> name="status<?php echo $patient['id'];?>" <?php if($patient['greetingStatus']==0) echo "checked"; ?> value="0">Да
            <input type="radio" id="greetingStatus<?php echo $patient['id'];?>"
                   <?php echo $disabledAttr; ?> name="status<?php echo $patient['id'];?>" <?php if($patient['greetingStatus']==1) echo "checked"; ?> value="1">Нет
            <div>
            <?php // echo $patient['greetingStatus']; ?>
        </td>
        <td>
            <?php 
				if($patient['medcard_id'] != null) {
					echo CHtml::link('<span class="glyphicon glyphicon-edit"></span>', array('#' . $patient['medcard_id']), array('title' => 'Посмотреть медкарту', 'class' => 'editMedcard'));
				}				
			?>
        </td>
        <!--<td>
                                <?php echo ($patient['is_beginned'] == 1 || $patient['is_accepted'] == 1) ? '' : CHtml::link('<span class="glyphicon glyphicon-flash"></span>', array('/doctors/shedule/acceptbegin/?id='.$patient['id']), array('title' => 'Начать приём')); ?>
                            </td>-->
        <td>
            <?php
            if (($patient['id'] == $currentSheduleId)&&(!($patient['is_accepted'] == 1 || $patient['is_beginned'] != 1))) {
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
<?php
?>