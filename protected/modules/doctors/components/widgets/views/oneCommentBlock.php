<?php
if (count($doctorComment)>0 && $doctorComment)
{
    $this->render('application.modules.doctors.components.widgets.views.comment', array(
        'commentAuthorFio' => $doctorComment['authorFio'],
        'commentDate' => $doctorComment['commentDate'],
        'commentText' => $doctorComment['comment'],
        'commentId' => $doctorComment['id'],
        'canEditComment' => $doctorComment['canEditComment']
    ));
}
?>
<?php
if ($numberDoctorComments>1)
{
    ?>
    <div id="moreCommentsButton">
        <strong><a href="#">Смотреть остальные...</a></strong>
    </div>
<?php
}
?>