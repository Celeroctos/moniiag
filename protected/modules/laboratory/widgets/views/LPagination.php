<?
/**
 * @var LPagination $this - Self instance
 * @var int $offset - Start offset
 * @var int $step - Counter accumulator
 */
?>

<nav>
	<ul class="pagination">
		<li <?= $this->getClick($this->page != 1, -1) ?>>
			<a href="javascript:void(0)" aria-label="Предыдущая">
				<span aria-hidden="true">&laquo;</span>
			</a>
		</li>
		<? if ($this->page > 1): ?>
			<li <?= $this->getClick(true, 1 - $this->page) ?>>
				<a href="javascript:void(0)"><?= 1 ?>
					<span class="sr-only"></span>
				</a>
			</li>
			<? if ($this->page > 2): ?>
				<li <?= $this->getClick(false, 0) ?>>
					<a href="javascript:void(0)">...
						<span class="sr-only"></span>
					</a>
				</li>
			<? endif; ?>
		<? endif; ?>
		<? for ($i = $this->page + $offset, $j = -1; $i <= $this->pages && $j < $this->limit; $i++, $j++): ?>
			<? if ($i < 1 || $i > $this->pages) {
				continue;
			} ?>
			<li <?= $this->getClick(true, $i - $this->page) ?>>
				<a href="javascript:void(0)"><?= $i ?>
					<span class="sr-only"></span>
				</a>
			</li>
		<? endfor; ?>
		<? if ($offset == 0): ?>
			<li <?= $this->getClick(false, 0) ?>>
				<a href="javascript:void(0)">...
					<span class="sr-only"></span>
				</a>
			</li>
			<li <?= $this->getClick(true, $this->pages - $this->page) ?>>
				<a href="javascript:void(0)"><?= $this->pages ?>
					<span class="sr-only"></span>
				</a>
			</li>
		<? endif; ?>
		<li <?= $this->getClick($this->page != $this->pages, 1) ?>>
			<a href="javascript:void(0)" aria-label="Следующая">
				<span aria-hidden="true">&raquo;</span>
			</a>
		</li>
	</ul>
</nav>