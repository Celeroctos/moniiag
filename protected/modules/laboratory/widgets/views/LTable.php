<?php
/**
 * @var LTable $this - Table widget instance
 * @var Array $table - Basic model's configuration
 * @var Array $data - Array with all received data
 */
?>

<table class="table table-striped table-bordered">
	<thead>
	<tr>
	<? foreach ($this->header as $key => $value): ?>
		<td id="<?=$value["id"]?>" class="<?=$value["class"]?>" style="<?=$value["style"]?>">
			<b><?=$this->header[$key]["label"]?></b>
		</td>
	<? endforeach; ?>
		<td align="middle" style="width: 50px"><b>@</b></td>
	</tr>
	</thead>
	<tbody>
	<? foreach ($data as $key => $value): ?>
		<tr>
			<? foreach ($this->header as $k => $v): ?>
				<td id="<?=$v["id"]."-",$value["id"]?>"><?=$value[$k]?></td>
			<? endforeach; ?>
			<td align="middle">
				<a href="javascript:void(0)"><span class="glyphicon glyphicon-pencil table-edit"></span></a>
				<a href="javascript:void(0)"><span class="glyphicon glyphicon-remove table-remove confirm"></span></a>
			</td>
		</tr>
	<? endforeach; ?>
	<? if (count($data) == 0): ?>
		<tr><td colspan="<?=count($this->header) + 1?>"><h2>Пусто</h2></td></tr>
	<? endif; ?>
	</tbody>
</table>