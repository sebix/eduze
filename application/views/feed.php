<?
include('inc/feed_head.php'); 
$textile = new Textile();
?>
<? if (isset($top)): echo $top; endif; ?>
<? foreach($entries->as_array() as $row): ?>
<entry>
	<? if (isset($row['title'])): /* no comment */ ?><title type="html"><?=$row['title']?></title>
		<id>http://www.example.com/post/<?=$row['cat']?>/<?=$row['name']?></id>
		<updated><?=$row['update']?></updated><? else: ?>
		<title type="html"><?=i18n::get('comment')?> #<?=$row['id']?></title>
		<id>http://www.example.com/post/<?=$row['cat']?>/<?=$row['name']?> <?=$row['id']?></id>
	<? endif; ?>
    <published><?=$row['time']?></published>
    <link href="<?=Kohana::$base_url?>post/<?=$row['cat']?>/<?=$row['name']?>" />
    <author>
	<name><?=$row['author']?></name>
    </author>
    <content type="xhtml"><div xmlns="http://www.w3.org/1999/xhtml">
	 <? if (isset($row['intro'])): ?>
	 	<p><strong>
			<?=$row['intro']?>
		</strong></p>
	<? endif; ?>
	<p>
		<?=$textile->TextileThis($row['body'])?>
	</p>
	<? if (isset($row['title'])): /* no comment */ ?>
		<p><?=i18n::get('by')?> <?=$row['author']?> <?=i18n::get('on')?> <?=$row['time']?> <?=i18n::get('in-cat')?> <?=Helper_Sebix::anchor('category/'.$row['cat'], $row['cat'], i18n::get('go-to-cat') . ' ' . $row['cat']);?><br />
		<a href="<?=Kohana::$base_url?>post/<?=$row['cat']?>/<?=$row['name']?>#comments"><?=i18n::get('comments')?></a><br />
	<? else: ?>
		<p><?=i18n::get('by')?> <?=$row['author']?> <?=i18n::get('on')?> <?=$row['time']?> <?=i18n::get('in-post')?> <?=Helper_Sebix::anchor('post/'.$row['cat'].'/'.$row['name'], $row['name'],i18n::get('go-to-post'));?><br />
	<? endif; ?>
	<? if(isset ($row['tags']) && is_array($row['tags'])): ?>Tags: <? foreach ($row['tags'] as $key => $val): ?>
		<? if ($key): ?>
			|
		<? endif; ?>
	<a href="<?=Kohana::$base_url?>tag/<?=$val?>" title="<?=i18n::get('view-article-tagged-with-1')?> <?=$val?> <?=i18n::get('view-article-tagged-with-2')?>"><?=$val?></a>
	<? endforeach; endif; ?></p>
</div></content>
</entry>

<? endforeach; ?>
<? if (isset($bottom)): echo $bottom; endif; ?>
<? include('inc/feed_foot.php'); ?>
