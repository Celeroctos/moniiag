<?php

/**
 * @var LCollapsible $this - Self instance
 */

?>

<a class="btn btn-primary" data-toggle="collapse" href="#<?= $this->id ?>" aria-expanded="false" aria-controls="<?= $this->id ?>">
	Link with href
</a>
<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#<?= $this->id ?>" aria-expanded="false" aria-controls="<?= $this->id ?>">
	Button with data-target
</button>
<div class="collapse" id="<?= $this->id ?>">
	<div class="well">
		...
	</div>
</div>