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
    <? if ($this->disableControl != true): ?>
		<td align="middle" style="width: 50px"><b></b></td>
    <? endif; ?>
	</tr>
	</thead>
	<tbody>
	<? foreach ($data as $key => $value): ?>
		<tr data-id="<?=$value[$this->pk]?>">
			<? foreach ($this->header as $k => $v): ?>
				<td align="left"><?=isset($value[$k]) ? $value[$k] : ""?></td>
			<? endforeach; ?>
            <? if ($this->disableControl != true): ?>
                <td align="middle">
                    <a href="javascript:void(0)"><span class="glyphicon glyphicon-pencil table-edit"></span></a>
                    <a href="javascript:void(0)"><span class="glyphicon glyphicon-remove table-remove confirm"></span></a>
                </td>
            <? endif; ?>
		</tr>
	<? endforeach; ?>
	<? if (count($data) == 0): ?>
		<tr><td colspan="<?=count($this->header) + 1?>"><b>Нет данных</b></td></tr>
	<? endif; ?>
	</tbody>
</table>