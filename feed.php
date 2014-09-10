<?php

require_once(dirname(__FILE__) . '/settings.php');
require_once(dirname(__FILE__) . '/log.php');
require_once(dirname(__FILE__) . '/plugin.php');
require_once(dirname(__FILE__) . '/function.php');

// http://codex.wordpress.org/WordPress_Feeds

/*
  Fake posts RSS feed
  to enable the IFTTT trigger part
  for the event "on new post"
  Their server query this feed periodically
  to see if they have to trigger some recipe.
*/

__log("RSS Feed Endpoint triggered");
  
?><?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
  xmlns:rawvoice="http://www.rawvoice.com/rawvoiceRssModule/">
<channel>
	<title>IFTTT Wordpress WebHook</title>
	<atom:link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/feed/" rel="self" type="application/rss+xml" />
	<link>http://<?php echo $_SERVER['HTTP_HOST']; ?></link>
	<description>Fake RSS feed to implement IFTTT Wordpress TRIGGER Events</description>
	<lastBuildDate><?php /* http://it2.php.net/manual/it/function.date.php */ echo date('D, d M Y H:i:s O'); /* Tue, 09 Sep 2014 15:35:33 +0000 */ ?></lastBuildDate>
	<language>it-IT</language>
  <sy:updatePeriod>hourly</sy:updatePeriod>
  <sy:updateFrequency>1</sy:updateFrequency>
	<generator>http://wordpress.org/?v=4.0</generator>
	<managingEditor>itomi@leganerd.com (Lega Nerd)</managingEditor>
	<item>
		<title>New fake post</title>
		<link>http://<?php echo $_SERVER['HTTP_HOST']; ?>/?postid=<?php echo rand(1231,123131); ?></link>
		<comments>http://<?php echo $_SERVER['HTTP_HOST']; ?>/?postid=<?php echo rand(1231,123131); ?>#comments</comments>
		<pubDate><?php echo date('D, d M Y H:i:s O'); /*Tue, 09 Sep 2014 15:35:01 +0000*/ ?></pubDate>
		<dc:creator><![CDATA[ifttt-webhook]]></dc:creator>
		<category><![CDATA[DIY]]></category>
		<guid isPermaLink="false">http://<?php echo $_SERVER['HTTP_HOST']; ?>/?postid=<?php echo rand(1231,123131); ?></guid>
		<description><![CDATA[{"action":"new_remote_event"}]]></description>
    <content:encoded><![CDATA[{"action":"new_remote_event"}]]></content:encoded>
    <wfw:commentRss>http://<?php echo $_SERVER['HTTP_HOST']; ?>/postid-<?php echo rand(1231,123131); ?>/feed/</wfw:commentRss>
		<slash:comments>12</slash:comments>
	</item>
</channel>
</rss>
