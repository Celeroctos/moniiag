<div class="header">
    <h3>Результаты приёма за <?php echo $greeting['date']; ?>, <?php echo $greeting['doctor_fio']; ?></h3>
</div>
<?php
foreach($categories as $categorie) {
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
<button class="printBtn default-margin-left">Напечатать результаты приёма</button>