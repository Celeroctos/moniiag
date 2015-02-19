<?php

/**
 * @var LCollapsible $this - Self instance
 */

?>

<a class="btn btn-primary" data-toggle="collapse" href="#<?= $this->id ?>" aria-expanded="true" aria-controls="<?= $this->id ?>">
    <div class="img-circle">
        <span class="glyphicon glyphicon-chevron-up"></span>
    </div>
</a>
<div class="collapse" id="<?= $this->id ?>">
    <div class="well">
        <h4>0x7b</h4>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#<?= $this->id ?>").collapse();
    });
</script>