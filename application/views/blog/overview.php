<? include('application/views/inc/head.php'); ?>

		<h2><?=$title?> <? if (isset($rss)): ?><a href="<?=$rss?>" title="Zum passenden RSS-Feed"><img src="/img/atom.png" style="height:20px; width:20px; border:0" alt="rss" /></a><? endif; ?></h2>
<? if (isset($top)): echo $top; endif; ?>
<? foreach($entries as $row): ?>
</div><div class="box">
		<h4><?=Helper_Sebix::anchor('post/'.$row['cat'].'/'.$row['name'], $row['title'], i18n::get('go-to-post') . ' '.$row['title']);?></h4>
		<span class="description"><?=i18n::get('by')?> <?=Helper_Sebix::anchor('about/' . $row['author'], $row['author'], i18n::get('go-to-user') . ' ' . $row['author'])?> <?=i18n::get('on')?> <?=$row['time']?><? if ($row['cat']): ?> <?=i18n::get('in-cat')?> <?=Helper_Sebix::anchor('category/'.$row['cat'], $row['cat'], i18n::get('go-to-cat') . ' '.$row['cat']); endif; ?></span>
		<p>
		<?
/*		$body = $row->body;
		if (strlen($body) > 500)
			$body = substr($body, 0 , 500) . "...<br />" . anchor('blog/'.$row->id.'/'.$row->short_name, "weiterlesen"); 
		echo str_replace($suchmuster, $ersetzungen, $body);
*/
		echo $row['intro'];
		?>
		</p>
		<span><?=Helper_Sebix::anchor('post/'.$row['cat'].'/'.$row['name'].'#comments', i18n::get('comments'), i18n::get('go-immediately-to-comments'));?></span>
<? endforeach; ?>

<? if (isset($bottom)): echo '</div><div class="box">' . $bottom; endif; ?>

<? include('application/views/inc/foot.php'); ?>
