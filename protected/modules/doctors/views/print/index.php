<table class="medcardIndex">
    <tbody>
        <tr>
            <td class="first" colspan="6"><?php echo $enterprise->fullname; ?></td>
        </tr>
        <tr>
            <td colspan="6" class="first">Код ОГРН <?php echo $enterprise->ogrn; ?></td>
        </tr>
        <tr>
            <td class="first" colspan="6">Медицинская карта амбулаторного больного № <strong class="bigger"><?php echo $medcard->card_number; ?></strong></td>
        </tr>
        <tr>
            <td colspan="2" class="first">1. Страховая мед. организация</td>
            <td colspan="4" strong class="big"><?php echo $oms->insurance; ?></td>
        </tr>
        <tr>
            <td colspan="2" class="first">2. Номер страхового полиса</td>
            <td colspan="4"><span class="big"><?php echo $oms->oms_number; ?></td>
        </tr>
		<tr>
		
			<td colspan="1"></td>
			<td class="first">Дата выдачи</td>
            <td><span class="big"><?php echo $oms->givedate; ?></td>
            <td class="first">Дата окончания</td>
            <td colspan="2"><span class="big"><?php echo $oms->enddate; ?></td>
		
		</tr>
        <tr>
            <td class="first">3. Код льготы</td>
            <td colspan="5">
            <?php
            foreach($privileges as $priv) {
                echo $priv['docname'].'<br />';
            }
            ?>
            </td>

        </tr>
		
		<tr>          
			<td colspan="1"></td>
			<td class="first">4. СНИЛС</td>
            <td colspan="1"><nobr><strong class="big"><?php echo $medcard->snils; ?><nobr></td>
		    <td class="first">Участок</td>
            <td><strong class="big"></td>
		</tr>
		
        <tr>
            <td class="first">5. Фамилия</td>
            <td colspan="5"><strong class="bigger"><?php echo $oms->last_name; ?></td>
        </tr>
        <tr>
            <td class="first">6. Имя</td>
            <td colspan="5"><strong class="bigger"><?php echo $oms->first_name; ?></td>
        </tr>
        <tr>
            <td class="first">7. Отчество</td>
            <td colspan="5"><strong class="bigger"><?php echo $oms->middle_name; ?></td>
        </tr>
        <tr>
            <td class="first">8. Пол</td>
            <td><strong class="bigger"><?php echo $oms->gender == 1 ? 'Муж' : 'Жен'; ?></td>
            <td colspan="2" class="first">9. Дата рождения</td>
            <td><strong class="big"><?php echo $oms->birthday; ?></td>
        </tr>
        <tr>
            <td colspan="6" class="first">10. Адрес постоянного места жительства</td>
            
        </tr>
		<tr>
			<td colspan="6"><strong class="medium"><?php echo $medcard->address_reg; ?></td>
		</tr>
        <tr>
            <td colspan="6" class="first">11. Адрес регистрации по месту пребывания</td>
           
        </tr>
		<tr>
			<td colspan="6"><span class="big"><?php echo $medcard->address; ?></td>
		</tr>
        <tr>
            <td class="first">12. Телефон</td>
            <td><span class="big"><?php echo $medcard->contact; ?></strong></td>
            <td colspan="2" class="first">Номер паспорта</td>
            <td><nobr><span class="big"><?php echo $medcard->serie; ?> <?php echo $medcard->docnumber; ?></nobr></td>
        </tr>
        <tr>
            <td class="first">13. Документ по льготе</td>
            <td colspan="6"><strong class="big">
            <?php
            foreach($privileges as $priv) {
                echo $priv['docname'].' № '.$priv['docserie'].' '.$priv['docnumber'].', выдан '.$priv['docgivedate'].'<br/>';
            }
            ?>
            </td>
        </tr>
        <tr>
            <td class="first">14. Инвалидность</td>
            <td colspan="5"><span class="big"><?php echo $medcard->invalid_group; ?></td>
        </tr>
        <tr>
            <td class="first">15. Место работы</td>
            <td colspan="5"><strong class="big"><?php echo $medcard->work_place; ?></td>
        </tr>
        <tr>
            <td class="first">16. Профессия</td>
            <td colspan="2"><strong class="big"><?php echo $medcard->profession; ?></td>
            <td class="first">Должность</td>
            <td colspan="3"><strong class="big"><?php echo $medcard->post; ?></td>
        </tr>
        <tr class="printBtnTr">
            <td class="first" colspan="6">
                <button class="printBtn">Напечатать титульную страницу медкарты</button>
            </td>
        </tr>
    </tbody>
</table>