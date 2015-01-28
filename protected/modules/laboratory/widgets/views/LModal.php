<?
/**
 * @var $this LModal - Modal widget component for laboratory module
 */
?>

<div class="modal fade" id="<?=$this->id?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=$this->title?></h4>
            </div>
            <div class="modal-body">
                <?=$this->body?>
            </div>
            <div class="modal-footer">
                <table width="100%">
                <tr>
                <td align="left">
                    <span class="glyphicon glyphicon-refresh refresh-button"></span>
                </td>
                <td>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= "Закрыть" ?></button>
                    <? if ($this->buttons != null): ?>
                        <? foreach ($this->buttons as $i => $button): ?>
                            <button <?=isset($button["attributes"]) ? $button["attributes"] : ""?> type="button" class="<?=$button["class"]?>"><?=$button["text"]?></button>
                        <? endforeach; ?>
                    <? endif; ?>
                </td>
                </tr>
                </table>
            </div>
        </div>
    </div>
</div>