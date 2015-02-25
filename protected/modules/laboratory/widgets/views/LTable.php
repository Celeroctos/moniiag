<?php
/**
 * @var LTable $this - Table widget instance
 * @var array $data - Array with all received data
 * @var string $parent - Parent's class name
 */
?>
<table class="table table-striped" data-condition="<?=$this->criteria->condition?>" data-parameters="<?=urlencode(serialize($this->criteria->params))?>" data-class="<?=$parent?>" id="<?=$this->id?>">
	<thead>
	<tr>
	<? foreach ($this->header as $key => $value): ?>
		<td data-key="<?=$key?>" onclick="Table.order.call(this, '<?=$key?>')" align="left" <?=$value["id"] ? "id=\"{$value["id"]}\"" : ""?> <?=$value["class"] ? "class=\"{$value["class"]}\"" : ""?> style="cursor: pointer;<?=$value["style"]?>">
			<b><?=$this->header[$key]["label"]?></b>
			<? if ($this->sort == $key && !$this->hideArrow): ?>
				<span class="glyphicon <?= $this->desc ? "glyphicon-chevron-up" : "glyphicon-chevron-down" ?>"></span>
			<? endif; ?>
		</td>
	<? endforeach; ?>
	<? if (count($this->controls) > 0): ?>
		<td align="middle" style="width: 50px"></td>
    <? endif; ?>
	</tr>
	</thead>
	<tbody>
	<? foreach ($data as $key => $value): ?>
		<tr data-id="<?= $value[$this->pk] ?>" <?= $this->click ? "onclick=\"{$this->click}(this, '{$value[$this->pk]}')\"" : "" ?>>
			<? foreach ($this->header as $k => $v): ?>
				<td align="left"><?= isset($value[$k]) ? $value[$k] : "" ?></td>
			<? endforeach; ?>
            <? if (count($this->controls) > 0): ?>
                <td align="middle">
					<? foreach ($this->controls as $c => $class): ?>
						<a href="javascript:void(0)"><span class="<?= $c." ".$class ?>"></span></a>
					<? endforeach; ?>
                </td>
            <? endif; ?>
		</tr>
	<? endforeach; ?>
	<? if (count($data) == 0): ?>
		<tr><td colspan="<?= count($this->header) + 1 ?>"><b>Нет данных</b></td></tr>
	<? endif; ?>
	</tbody>
	<? if (!$this->disablePagination): ?>
	<tfoot>
	<tr><td colspan="<?= count($this->header) + 1 ?>">
		<? $this->widget("LPagination", [
			"limit" => 10,
			"action" => "Table.page.call",
			"page" => $this->page,
			"pages" => $this->pages
		]); ?>
	</td></tr>
	</tfoot>
	<? endif; ?>
</table>