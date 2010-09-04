<? include('application/views/inc/head.php'); ?>
<?=$top?>
		<h3><?=Helper_Sebix::anchor('post/'.$cat.'/'.$short_name, $title);?></h3>
		
		<span class="description"><?=i18n::get('by')?> <?=Helper_Sebix::anchor('about/' . $author, $author, i18n::get('go-to-user') . ' ' . $author)?> <?=i18n::get('on')?> <?=$time?> <?=i18n::get('in-cat')?> <?=Helper_Sebix::anchor('category/'.$cat, $cat, i18n::get('go-to-cat') . ' '.$cat);?></i></span>
		<p><strong>
		<?=$intro ?>
		</strong></p>
		<p>
		<?=$body?>
		</p>
<p>Tags:
<? foreach ($tags as $key => $val): ?>
	<? if ($key): ?>
		|
	<? endif; ?>
	<?=Helper_Sebix::anchor('tag/' . $val, $val, i18n::get('view-article-tagged-with-1') . ' ' . $val . ' ' . i18n::get('view-article-tagged-with-2'))?>
<? endforeach; ?>
</p>

	<h3 id="comments"><?=i18n::get('comments')?> <? if (isset($feed)): ?><?=Helper_Sebix::anchor($feed, '<img src="/img/atom.png" style="height:20px; width:20px; border:0" alt="feed" />', i18n::get('go-to-fitting-feed'))?><? endif; ?></h3>
<? if ($comments): ?>
	<? $color = true; ?>
	<? foreach($comments->as_array() as $row): $color?$color=false:$color=true ?>
		<div class="comment<?=$color?"2":""?>">
		<? if (empty($row['url'])): ?>
		<span class="description"><?=$row['author']?>
		<? else: ?>
		<span class="description"><?=Helper_Sebix::anchor($row['url'],$row['author'],i18n::get('go-to-website-of-1') . ' ' . $row['author'] . ' ' . i18n::get('go-to-website-of-2'))?>
		<? endif; ?>
		 <?=i18n::get('says-on')?> <?=$row['time']?>:</span>
	
		<p><?=htmlentities($row['body'],ENT_QUOTES,'UTF-8')?></p>
		</div>
	<? endforeach; ?>
<? else: ?>
	<p><?=i18n::get('no-comments')?></p>
<? endif; ?>
<h4><?=i18n::get('new-comment')?></h4>
<?
$attributes = array('id' => 'comments');
echo form::open('blog/comment_insert/' . $id, $attributes);?>
	<p><label for="body"><?=i18n::get('your-text')?>:</label><br />
	<textarea name="body" rows="10"><?=@$form['body']?></textarea>
	</p>
	<p><label for="author"><?=i18n::get('your-name')?>:</label><br />
	<input type="text" name="author" value="<?=@$form['author']?>" />
	</p>
	<p><label for="mail"><?=i18n::get('your-mail')?>:</label><br />
	<input type="text" name="mail" value="<?=@$form['mail']?>" />
	</p>
	<p><label for="url"><?=i18n::get('your-url')?>:</label><br />
	<input type="text" name="url" value="<?=@$form['url']?>" />
	</p>
	<p>
	<span><?=i18n::get('avoid-spam')?>:<br /></span>
	<? if ( ! $captcha->promoted()): ?>
		<?=$captcha->render()?>
		<br />
		<?=form::input('captcha_response')?>
	<? endif; ?>
	<br />
	<span style="font-size:10px">(Groß-/Kleinschreibung ist egal)</span>
	</p>
	<p>
	<input type="submit" value="<?=i18n::get('submit')?>" class="submit" />
	</p>
<?=form::close()?>

<? include('application/views/inc/foot.php'); ?>
