<? header('Content-type: application/atom+xml'); ?>
<?='<?xml version="1.0" encoding="utf-8"?>'?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title>Xebi Blog <?=$title?></title>
  <author>
    <name>Sebastian Wagner</name>
  </author>
  <id>http://www.example.com/</id>
  <updated>2010-01-21T17:00:00Z</updated>
  <link href="http://www.example.com" />
  <link href="http://www.example.com/feed/<?=$feed_link?>" rel="self" />
  <subtitle>Das ist der Xebi Blog Hier gibt es Berichte, Tipps und vieles mehr zu Linux (Debian, Ubuntu) und Programmierung, wie PHP, C++ und Java.</subtitle>
  <icon>http://www.example.com/favicon.ico</icon>
  <rights>http://www.example.com/about/copyright</rights>
