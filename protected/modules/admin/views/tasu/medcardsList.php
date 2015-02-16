<table>
    <thead>
        <tr>
            <td>Номер карты</td>
            <td>ФИО пациента</td>
            <td>Номер док-та</td>
            <td>Полис</td>
            <td>Дата выдачи</td>
            <td>Статус полиса</td>
            <td>Адрес</td>
            <td>Страх. компания</td>
            <td>Регион</td>
        </tr>
    </thead>
    <?php foreach($medcards as $card) : ?>
        <tr>
            <td><?php echo $card['medcard']; ?></td>
            <td><?php echo $card['patient_fio']; ?></td>
            <td><?php echo $card['docdata']; ?></td>
            <td><?php echo $card['oms_series_number']; ?></td>
            <td><?php echo $card['givedate']; ?></td>
            <td><?php echo $card['status']; ?></td>
            <td><?php echo $card['address_str']; ?></td>
            <td><?php echo $card['insurance']; ?></td>
            <td><?php echo $card['region']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>