<p class="pagination">

	<?php if ($previous_page): ?>
		<a href="<?php echo str_replace('{page}', $previous_page, $url) ?>">&laquo;&nbsp;<?php echo Kohana::lang('pagination.previous') ?></a>
	<?php else: ?>
		&laquo;&nbsp;<?php echo Kohana::lang('pagination.previous') ?>
	<?php endif ?>

	| <?php if ($current_page > 3): 	// PunBB ?>
		<a href="<?php echo str_replace('{page}', 1, $url) ?>">1</a>
		<?php if ($current_page != 4) echo '&hellip;' ?>
	<?php endif ?>


	<?php for ($i = $current_page - 2, $stop = $current_page + 3; $i < $stop; ++$i): ?>

		<?php if ($i < 1 OR $i > $total_pages) continue ?>

		<?php if ($current_page == $i): ?>
			<strong><?php echo $i ?></strong>
		<?php else: ?>
			<a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a>
		<?php endif ?>

	<?php endfor ?>


	<?php if ($current_page <= $total_pages - 3): ?>
		<?php if ($current_page != $total_pages - 3) echo '&hellip;' ?>
		<a href="<?php echo str_replace('{page}', $total_pages, $url) ?>"><?php echo $total_pages ?></a>
	<?php endif ?>

	| <?php if ($next_page): ?>
		<a href="<?php echo str_replace('{page}', $next_page, $url) ?>"><?php echo Kohana::lang('pagination.next') ?>&nbsp;&raquo;</a>
	<?php else: ?>
		<?php echo Kohana::lang('pagination.next') ?>&nbsp;&raquo;
	<?php endif ?>

<br />

	<?php echo Kohana::lang('pagination.page') ?> <?php echo $current_page ?> <?php echo Kohana::lang('pagination.of') ?> <?php echo $total_pages ?>
	| <?php echo Kohana::lang('pagination.items') ?> <?php echo $current_first_item ?>&ndash;<?php echo $current_last_item ?> <?php echo Kohana::lang('pagination.of') ?> <?php echo $total_items ?>

</p>
