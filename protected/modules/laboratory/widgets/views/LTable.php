<?php
/**
 * @var LTable $this - Table widget instance
 * @var Array $table - Basic model's configuration
 * @var Array $data - Array with all received data
 * @var String $toggle - Modal id to toggle
 */
?>

<table class="table table-striped table-hover" id="<?=$this->id?>">
	<thead>
	<tr>
	<? foreach ($this->header as $key => $value): ?>
		<td align="left" id="<?=$value["id"]?>" class="<?=$value["class"]?>" style="<?=$value["style"]?>">
			<b><?=$this->header[$key]["label"]?></b>
		</td>
	<? endforeach; ?>
		<td align="middle" style="width: 50px"><b></b></td>
	</tr>
	</thead>
	<tbody>
	<? foreach ($data as $key => $value): ?>
		<tr data-id="<?=$value["id"]?>">
			<? foreach ($this->header as $k => $v): ?>
				<td align="left"><?=$value[$k]?></td>
			<? endforeach; ?>
			<td align="middle">
				<a href="javascript:void(0)"><span class="glyphicon glyphicon-pencil table-edit"></span></a>
				<a href="javascript:void(0)"><span class="glyphicon glyphicon-remove table-remove confirm"></span></a>
			</td>
		</tr>
	<? endforeach; ?>
	<? if (count($data) == 0): ?>
		<tr><td colspan="<?=count($this->header) + 1?>"><b>Нет данных</b></td></tr>
	<? endif; ?>
	</tbody>
</table>