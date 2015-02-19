<?php
/**
 * @var LTable $this - Table widget instance
 * @var array $data - Array with all received data
 * @var string $parent - Parent's class name
 */
const PAGE_LIMIT = 10;
?>
<table class="table table-striped table-hover" data-condition="<?=$this->criteria->condition?>" data-parameters="<?=urlencode(serialize($this->criteria->params))?>" data-class="<?=$parent?>" id="<?=$this->id?>">
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
		<tr data-id="<?=$value[$this->pk]?>">
			<? foreach ($this->header as $k => $v): ?>
				<td align="left"><?=isset($value[$k]) ? $value[$k] : ""?></td>
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
		<tr><td colspan="<?=count($this->header) + 1?>"><b>Нет данных</b></td></tr>
	<? endif; ?>
	</tbody>
	<? if (!$this->disablePagination): ?>
	<tfoot>
	<tr><td colspan="<?=count($this->header) + 1?>">
		<nav>
			<ul class="pagination">
				<li onclick="Table.page.call(this, <?=$this->page-1?>)" <?= $this->page == 1 ? "class=\"disabled\"" : "" ?>>
					<a href="javascript:void(0)" aria-label="Предыдущая">
						<span aria-hidden="true">&laquo;</span>
					</a>
				</li>
				<? if ($this->page != 1): ?>
					<? for ($i = 1; $i <= 1; $i++): ?>
						<li onclick="Table.page.call(this, <?=$i?>)" <?= $this->page == $i ? "class=\"active\"" : "" ?>>
							<a href="javascript:void(0)"><?=$i?>
								<span class="sr-only"></span>
							</a>
						</li>
					<? endfor; ?>
					<? if ($i < $this->page): ?>
						<li class="disabled">
							<a href="javascript:void(0)" aria-label="Empty">
								<span aria-hidden="true">...</span>
							</a>
						</li>
					<? endif; ?>
				<? endif; ?>
				<? for ($i = $this->page; $i <= $this->pages && $i <= PAGE_LIMIT + $this->page; $i++): ?>
					<li onclick="Table.page.call(this, <?=$i?>)" <?= $this->page == $i ? "class=\"active\"" : "" ?>>
						<a href="javascript:void(0)"><?=$i?>
							<span class="sr-only"></span>
						</a>
					</li>
				<? endfor; ?>
				<? if ($i > PAGE_LIMIT): ?>
					<li class="disabled">
						<a href="javascript:void(0)" aria-label="Empty">
							<span aria-hidden="true">...</span>
						</a>
					</li>
				<? endif; ?>
				<li <?= $this->page != $this->pages ? "onclick=\"Table.page.call(this, <?=$this->page+1?>)\"" : "" ?> <?= $this->page == $this->pages ? "class=\"disabled\"" : "" ?>>
					<a href="javascript:void(0)" aria-label="Следующая">
						<span aria-hidden="true">&raquo;</span>
					</a>
				</li>
			</ul>
		</nav>
	</td></tr>
	</tfoot>
	<? endif; ?>
</table>