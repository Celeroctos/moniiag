<?
/**
 * LPanel $this - Widget's controller with render properties
 */
?>

<div class="panel panel-default" id="<?=$this->id?>">
    <div class="panel-heading">
        <table width="100%">
            <tr><td style="font-size: 15px"><b><?=$this->title?></b></td>
            <td align="right">
                <span class="glyphicon glyphicon-refresh refresh-button hidden"></span>
                <div class="img-circle">
                    <span class="glyphicon glyphicon-chevron-up collapse-button <?= !$this->collapse ? "hidden" : "" ?>"></span>
                </div>
            </td>
            </tr>
        </table>
    </div>
    <div class="panel-body">
        <?=$this->body?>