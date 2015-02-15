<?
/**
 * @var $this LGuideValueEditor - Widget's instance
 * @var $columns Array - Array with guide columns
 * @var $values Array - Array with guide values
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
        <? if (count($values) == 0): ?>
            <? for ($i = 0; $i < LGuideValueEditor::DEFAULT_COUNT; $i++): ?>
                <tr>
                    <? foreach ($columns as $column): ?>
                        <td data-position="<?= $column->position ?>">
                            <?= $this->renderField($column->type, $column->name, $column->default_value) ?>
                        </td>
                    <? endforeach; ?>
                    <td><span style="font-size: 15px; margin-top: 7px" class="glyphicon glyphicon-remove glyphicon-red remove"></span></td>
                </tr>
            <? endfor; ?>
        <? else: ?>
            <? foreach ($values as $value): ?>
                <tr data-id="<?= $value[0]["guide_row_id"] ?>">
                    <? foreach ($columns as $column): ?>
                        <? if (isset($value[$column->position - 1])): ?>
                            <td data-position="<?= $column->position ?>" data-id="<?= $value[$column->position - 1]["id"] ?>">
                                <?= $this->renderField($column->type, $column->name, $value[$column->position - 1]["value"],
                                    $column->lis_guide_id, $column->display_id) ?>
                            </td>
                        <? else: ?>
                            <td data-position="<?= $column->position ?>">
                                <?= $this->renderField($column->type, $column->name, $column->default_value) ?>
                            </td>
                        <? endif; ?>
                    <? endforeach; ?>
                    <td><span style="font-size: 15px; margin-top: 7px" class="glyphicon glyphicon-remove glyphicon-red remove"></span></td>
                </tr>
            <? endforeach; ?>
        <? endif; ?>
        </tbody>
    </table>
    <div style="width: 100%; text-align: right">
        <a href="javascript:void(0)" id="guide-edit-add-fields">
            <span style="font-size: 20px" class="glyphicon glyphicon-plus"></span>
        </a>
    </div>
</div>