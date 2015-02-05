<?
/**
 * @var $this LGuideValues - Widget's instance
 * @var $columns Array - Array with guide columns
 */
?>

<div class="col-xs-12 col-xs-offset-0">
    <table class="table table-striped table-bordered" width="100%">
        <thead>
        <tr>
            <? foreach ($columns as $column): ?>
                <td><b><?= $column->name ?></b></td>
            <? endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <? for ($i = 0; $i < LGuideValues::DEFAULT_COUNT; $i++): ?>
            <tr>
                <? foreach ($columns as $column): ?>
                    <td><?= $this->renderField($column->type, $column->name) ?></td>
                <? endforeach; ?>
            </tr>
        <? endfor; ?>
        </tbody>
    </table>
</div>