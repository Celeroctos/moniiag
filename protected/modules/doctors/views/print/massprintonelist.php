<?php
foreach($greetings as $greeting) {
?>
<div class="bottom-border-dotted">
    <div class="header">
        <h3>Результаты приёма за <?php echo $greeting['greeting']['date']; ?>, <?php echo $greeting['greeting']['doctor_fio']; ?> (пациент <?php echo $greeting['greeting']['patient_fio']; ?>, номер карты <?php echo $greeting['greeting']['card_number']; ?>)</h3>
    </div>
    <?php
    foreach($greeting['categories'] as $categorie) {
        ?>
        <div class="categorie">
            <h4><?php echo $categorie[0]['info']['categorie_name']; ?></h4>
            <?php
            foreach($categorie as $element) {
                ?>
                <div class="field">
                    <strong><?php echo $element['info']['label']; ?>:</strong> <?php echo $element['element']['value']; ?>
                </div>
            <?php
            }
            ?>
        </div>
    <?
    }
    ?>
</div>
<?php
}
?>
<button class="printBtn default-margin-left">Напечатать результаты приёма</button>