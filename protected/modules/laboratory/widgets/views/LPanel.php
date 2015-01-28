<?
/**
 * LPanel $this - Widget's controller with render properties
 */
?>

<div class="panel panel-default" id="<?=$this->id?>">
    <div class="panel-heading">
        <table width="100%">
            <tr>
                <td>
                    <h4><?=$this->title?></h4>
                </td>
                <td align="right">
                    <span class="glyphicon glyphicon-refresh refresh-button"></span>
                </td>
            </tr>
        </table>
    </div>
    <div class="panel-body">
        <?=$this->body?>
    </div>
</div>