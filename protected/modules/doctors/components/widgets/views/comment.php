<!--div class="col-xs-12"-->
<div class="commentsContainer">

    <div class="commentsHandleContainer">
        <!-- ФИО врача -->
        <p><?php echo $commentDate ?>  |  <?php echo $commentAuthorFio ?>        <?php

            if ($canEditComment==1)
            {
                ?><span class="glyphicon glyphicon-pencil editComment" id="commentId<?php echo $commentId; ?>" title="Редактировать"><?php
            }
            ?></p>
    </div>
    <div class="commentTextContainer">
        <p><?php echo $commentText ?></p>
    </div>
</div>