<?php
/**
 * @var LMedcardNav $this - Self instance
 * @var string $module - Current module name
 * @var string $action - Current action name
 * @var string $controller - Current controller name
 * @var array $list - List with render actions
 */
?>

<ul class="nav nav-tabs">
    <? foreach ($list as $key => $link): ?>
        <li role="presentation" class="<?= $link["module"] == $module && $link["controller"] == $controller && $link["action"] == $action ? "active" : "" ?>">
            <a href="<?= Yii::app()->getBaseUrl()."/{$link["module"]}/{$link["controller"]}/{$link["action"]}" ?>"><?= $key ?></a>
        </li>
    <? endforeach; ?>
</ul>