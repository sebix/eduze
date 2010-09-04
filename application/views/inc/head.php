<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>

<title>eduze: <?=$title?></title>

<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<meta name="description" content="description"/>
<meta name="keywords" content="keywords"/> 
<meta name="author" content="author"/> 
<link rel="stylesheet" type="text/css" href="/stylesheets/style.css" media="screen"/>

<? if (isset($feed)): // additional atom-feed (for example: comments-feed ?>
	<link rel="alternate" type="application/atom+xml" title="Atom-Feed (passend)" href="<?=$feed?>" />
<? endif; ?>
	<link rel="alternate" type="application/atom+xml" title="Atom-Feed (Alle Beiträge)" href="/feed/" />
<? if (isset($head)): ?>
	<?=$head?>
<? endif; ?>

</head>

<body>

<div class="container">	
	<div class="main">
		<div class="header">
		
			<div class="title">
				<h1><?=$title?></h1>
			</div>

		</div>
		
		<div class="content">
	
			<div class="box">


