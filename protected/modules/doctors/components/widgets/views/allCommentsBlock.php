<div id="allCommentsBlock">
    <!-- Перебираем все комментарии и вызываем для них виджет comment -->
    <?php
        foreach ($allComments as $oneComment)
        {
            ?><div><?php
            $this->render('application.modules.doctors.components.widgets.views.comment', array(
                'commentAuthorFio' => $oneComment['authorFio'],
                'commentDate' => $oneComment['commentDate'],
                'commentText' => $oneComment['comment'],
                'commentId' => $oneComment['id'],
                'canEditComment' => $oneComment['canEditComment']
            ));
        }
    ?>
</div>