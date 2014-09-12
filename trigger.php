<?php

defined('__COMMON__') or die('Direct access not allowed here');

/*
	Here I can decide whenever to send or not
	the Trigger event.
	It's better to manage a queue for the events
	to trig, to avoid duplicate activated recipes.
*/
$something_to_post = true;

$obj = new stdClass;

/*
	Here you have to setup the fake
	post body
*/
$obj->post_event_id = rand(1,2999);
$obj->title = "my new post title";
$obj->date = date('D, d M Y H:i:s O'); 
$obj->body = '{"action":"new_remote_event"}';

if( defined('DO_TRIGGER') ) { // or failure(400);

	success('<array><data></data></array>');

	/*
  // send a fake blog post to trigger the event
  // "wordpress"::"onNewPost" on IFTTT
  // http://codex.wordpress.org/XML-RPC_WordPress_API/Posts
  // http://codex.wordpress.org/XML-RPC_MetaWeblog_API
  success('  <array>
    <data>
      <value>
        <struct>
        <member><name>postid</name><value>'.rand(199,99999).'</value></member>
        <member><name>title</name><value>Fake Very Post</value></member>
        <member><name>description</name><value>a new fake post</value></member>
        <member><name>link</name><value></value></member>
        <member><name>userid</name><value>0</value></member>
        <member><name>dateCreated</name><value></value></member>
        <member><name>date_created_gmt</name><value></value></member>
        <member><name>date_modified</name><value></value></member>
        <member><name>date_modified_gmt</name><value></value></member>
        <member><name>wp_post_thumbnail</name><value></value></member>
        <member><name>permaLink</name><value></value></member>
        <member><name>categories</name><value><array></array></value></member>
        <member><name>mt_keywords</name><value></value></member>
        <member><name>mt_excerpt</name><value></value></member>
        <member><name>mt_text_more</name><value></value></member>
        <member><name>wp_more_text</name><value></value></member>
        <member><name>mt_allow_comments</name><value>0</value></member>
        <member><name>mt_allow_pings</name><value>0</value></member>
        <member><name>wp_slug</name><value></value></member>
        <member><name>wp_password</name><value></value></member>
        <member><name>wp_author_id</name><value></value></member>
        <member><name>wp_author_display_name</name><value></value></member>
        <member><name>post_status</name><value>publish</value></member>
        <member><name>wp_post_format</name><value></value></member>
      </struct>
    </value>
  </data>
</array>');
	*/
	
	return;
}

if( defined('__WPINDEX__') ) { /// or die('Direct access not allowed here');

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
	<description>Fake RSS feed to implement IFTTT Wordpress TRIGGER Event</description>
	<lastBuildDate><?php 
		// http://it2.php.net/manual/it/function.date.php
		echo date('D, d M Y H:i:s O');
		// Tue, 09 Sep 2014 15:35:33 +0000
	?></lastBuildDate>
	<language>it-IT</language>
	<sy:updatePeriod>hourly</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>
	<generator>https://bitbucket.org/lidio601/ifttt-wordpress-gateway/</generator>
	<managingEditor>lidio601</managingEditor>
	<?php
	if($something_to_post) {
	?><item>
		<title><?php echo $obj->title; ?></title>
		<link>http://<?php echo $_SERVER['HTTP_HOST']; ?>/?postid=<?php echo $obj->post_event_id; ?></link>
		<comments>http://<?php echo $_SERVER['HTTP_HOST']; ?>/?postid=<?php echo $obj->post_event_id; ?>#comments</comments>
		<pubDate><?php echo $obj->date; /*Tue, 09 Sep 2014 15:35:01 +0000*/ ?></pubDate>
		<dc:creator><![CDATA[lidio601 https://bitbucket.org/lidio601/ifttt-wordpress-gateway/]]></dc:creator>
		<category><![CDATA[DIY]]></category>
		<guid isPermaLink="false">http://<?php echo $_SERVER['HTTP_HOST']; ?>/?postid=<?php echo $obj->post_event_id; ?></guid>
		<description><![CDATA[<?php echo $obj->body; ?>]]></description>
		<content:encoded><![CDATA[<?php echo $obj->body; ?>]]></content:encoded>
		<wfw:commentRss>http://<?php echo $_SERVER['HTTP_HOST']; ?>/postid-<?php echo $obj->post_event_id; ?>/feed/</wfw:commentRss>
		<slash:comments>0</slash:comments>
	</item>
<?php } ?>
</channel>
</rss>
<?php
	
	return;
	
}

?>