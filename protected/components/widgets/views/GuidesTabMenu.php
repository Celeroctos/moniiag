<ul class="nav nav-tabs  default-margin-bottom">
    <?php
    if (Yii::app()->user->checkAccess('guideEditEnterprise') ||
            Yii::app()->user->checkAccess('guideAddEnterprise') ||
            Yii::app()->user->checkAccess('guideDeleteEnterprise')
    ) {
        ?>
        <li <?php echo $controller == 'enterprises' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Учреждения', array('/guides/enterprises/view')) ?>
        </li>
    <?php } ?>
    <?php
    if (Yii::app()->user->checkAccess('guideAddWard') ||
            Yii::app()->user->checkAccess('guideEditWard') ||
            Yii::app()->user->checkAccess('guideDeleteWard')
    ) {
        ?>
        <li <?php echo $controller == 'wards' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Отделения', array('/guides/wards/view')) ?>
        </li>
    <?php } ?>
    <?php
    if (Yii::app()->user->checkAccess('guideAddMedworker') ||
            Yii::app()->user->checkAccess('guideEditMedworker') ||
            Yii::app()->user->checkAccess('guideDeleteMedworker')
    ) {
        ?>
        <li <?php echo $controller == 'medworkers' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Должности', array('/guides/medworkers/view')) ?>
        </li>
    <?php } ?>
    <?php
    if (Yii::app()->user->checkAccess('guideAddEmployee') ||
            Yii::app()->user->checkAccess('guideDeleteEmployee') ||
            Yii::app()->user->checkAccess('guideEditEmployee')
    ) {
        ?>
        <li <?php echo $controller == 'employees' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Сотрудники', array('/guides/employees/view')) ?>
        </li>
        <?php } ?>
        <?php
        if (Yii::app()->user->checkAccess('guideAddContact') ||
                Yii::app()->user->checkAccess('guideEditContact') ||
                Yii::app()->user->checkAccess('guideDeleteContact')
        ) {
            ?>
        <li <?php echo $controller == 'contacts' && $action == 'view' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('Контакты', array('/guides/contacts/view')) ?>
        </li>
    <?php } ?>
    <?php
    if (Yii::app()->user->checkAccess('guideAddCabinet') ||
            Yii::app()->user->checkAccess('guideEditCabinet') ||
            Yii::app()->user->checkAccess('guideDeleteCabinet')
    ) {
        ?>
        <li <?php echo $controller == 'cabinets' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Кабинеты', array('/guides/cabinets/view')) ?>
        </li>
    <?php } ?>
    <?php
    if (Yii::app()->user->checkAccess('guideAddPrivelege') ||
            Yii::app()->user->checkAccess('guideEditPrivelege') ||
            Yii::app()->user->checkAccess('guideDeletePrivelege')
    ) {
        ?>
        <li <?php echo $controller == 'privileges' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Льготы', array('/guides/privileges/view')) ?>
        </li>
    <?php } ?>
        <li <?php echo $controller == 'mkb10' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('МКБ-10', array('/guides/mkb10/view')) ?>
        </li>
        <li <?php echo $controller == 'service' && $action == 'view' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Медуслуги', array('/guides/service/view')) ?>
        </li>
        <li <?php echo $controller == 'insurances' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Страховые компании', array('/guides/insurances/view')) ?>
        </li>
        <li <?php echo $controller == 'cladr' ? 'class="active"' : ''; ?>>
            <?php echo CHtml::link('КЛАДР', array('/guides/cladr/viewregions')) ?>
        </li>
        <li <?php echo $controller == 'doctype' ? 'class="active"' : ''; ?>>
        <?php echo CHtml::link('Удостоверения личности', array('/guides/doctype/view')) ?>
        </li>
<?php
if (Yii::app()->user->checkAccess('guideAddMedcardRule') ||
        Yii::app()->user->checkAccess('guideEditMedcardRule') ||
        Yii::app()->user->checkAccess('guideDeleteMedcardRule') ||
        Yii::app()->user->checkAccess('guideAddMedcardPrefix') ||
        Yii::app()->user->checkAccess('guideEditMedcardPrefix') ||
        Yii::app()->user->checkAccess('guideDeleteMedcardPrefix') ||
        Yii::app()->user->checkAccess('guideAddMedcardPostfix') ||
        Yii::app()->user->checkAccess('guideEditMedcardPostfix') ||
        Yii::app()->user->checkAccess('guideDeleteMedcardPostfix') ||
        Yii::app()->user->checkAccess('guideAddMedcardSeparator') ||
        Yii::app()->user->checkAccess('guideEditMedcardSeparator') ||
        Yii::app()->user->checkAccess('guideDeleteMedcardSeparator') 
) {
    ?>
        <li <?php echo $controller == 'medcards' ? 'class="active"' : ''; ?>>
    <?php echo CHtml::link('Номера медкарт', array('/guides/medcards/viewprefixes')) ?>
        </li>
<?php } ?>
<?php
if (Yii::app()->user->checkAccess('guideEditAnalysisParam') ||
        Yii::app()->user->checkAccess('guideEditAnalysisType') ||
        Yii::app()->user->checkAccess('guideEditAnalysisTypeTemplate') ||
        Yii::app()->user->checkAccess('guideEditAnalyzerType') ||
        Yii::app()->user->checkAccess('guideEditAnalyzerTypeAnalysis') ||
        Yii::app()->user->checkAccess('guideEditAnalysisSampleType')
) {
    ?>
        <li <?php echo
    ($controller == 'analysisparam') ? 'class="active"' : '';
    ?>>
    <?php echo CHtml::link('Лаборатория', array('/guides/laboratory/analysisparam')) ?>
        </li>
<?php } ?>
</ul>