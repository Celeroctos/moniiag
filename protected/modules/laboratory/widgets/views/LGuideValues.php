<?
/**
 * @var $this LGuideValues - Widget's instance
 * @var $columns Array - Array with guide columns
 */
?>

<div class="col-xs-12 col-xs-offset-0 guide-values-container">
    <table class="table" width="100%">
        <thead>
        <tr>
            <? foreach ($columns as $column): ?>
                <td><b><?= $column->name ?></b></td>
            <? endforeach; ?>
            <td></td>
        </tr>
        </thead>
        <tbody>
        <? for ($i = 0; $i < LGuideValues::DEFAULT_COUNT; $i++): ?>
            <tr>
                <? foreach ($columns as $column): ?>
                    <td><?= $this->renderField($column->type, $column->name) ?></td>
                <? endforeach; ?>
                <td><span style="font-size: 15px; margin-top: 7px" class="glyphicon glyphicon-remove glyphicon-red remove"></span></td>
            </tr>
        <? endfor; ?>
        </tbody>
    </table>
    <div style="width: 100%; text-align: right">
        <a href="javascript:void(0)" id="guide-edit-add-fields">
            <span style="font-size: 20px" class="glyphicon glyphicon-plus"></span>
        </a>
    </div>
</div>