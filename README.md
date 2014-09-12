ifttt-wordpress-gateway
=======================

Project forked from the webhook middleware project for the ifttt.com service.
The original project is: <https://github.com/mapkyca/ifttt-webhook>

IFTTT uses wordpress-xmlrpc to communicate with the wordpress blog. We present a fake-xmlrpc interface on the website, which causes IFTTT to be fooled into thinking of this as a genuine wordpress blog. The only action that ifttt allows for wordpress are posting, which are instead used for powering webhooks. All the other fields (title, description, categories) along with the username/password credentials are passed along by the webhook. Do not use the "Create a photo post" action for wordpress, as ifttt manually adds a `<img>` tag in the description pointing to what url you pass. Its better to pass the url in clear instead (using body/category/title fields).

#How It Works

I've derived from that the ability to receive *fake-post web triggered events* and fire custom web event like:
  * android push notification
  * ios push notification
  * debug emails
  * other custom web call

I've removed the *fake plugin* support because I'm going to consider only the *fake post body* and ignore the rest of the data that is coming with the triggere event from the IFTTT service.

#For developers

I've managed all the code so you only need to work on these files:
	* action.php
	* trigger.php
	* settings.php

If you don't need the IFTTT Trigger or IFTTT Action you could only disable that part in the `settings.php` file.

#How To Use

It can support user authentication by the login-password specified on the Wordpress Channel creation:

![IFTTT Wordpress Channel setup](http://imgur.com/geTEZrr.png?1 "You can type in any username/password you want or a secret combination that is known by the project authentication process (not implemented yet)")

But the authentication is not currently implemented.

Another important change that I've made is:

A simplified of the *IFTTT Recipe Creation*
-------------------------------------------

If you want to create a Recipe that from a trigger fire a Web event throught this project site you only have to specify the json string that identify the remote action requested without any other information.
Here is an example:

![IFTTT Wordpress Recipe setup 1](http://imgur.com/hKnfN9J.png?1 "Select Wordpress ad DESTINATION Channel")

![IFTTT Wordpress Recipe setup 2](http://imgur.com/AMI1ixN.png?1 "Pick the create-a-post Action")

![IFTTT Wordpress Recipe setup 2](http://imgur.com/CVlMBui.png?1 "Blank all the field exept for the Body Field")

In the body field you can simply specify a JSON string like this:

    {
      "action":"my_very_awesome_action",
      "params":"some useful arguments"
    }

With this method you have the ability to implement your custom action in the *xmlrpc.php* file.

*At last but not least*

You can use the Fake Wordpress Channel to trig an event!
--------------------------------------------------------

I've implemented a fake RSS Feed so that the IFTTT service can query for new posts.

In the IFTTT Recipe creation process you now can use the wordpress channel:

![IFTTT Wordpress Triggered Recipe setup 1](http://imgur.com/gftpPn0.png?1 "Select the fake wordpress channel")

![IFTTT Wordpress Triggered Recipe setup 2](http://imgur.com/T8W9RZy.png?1 "Now select the on-new-post event")

An now you can specify whatever triggered event you need!

![IFTTT Wordpress Triggered Recipe setup 3](http://imgur.com/CQa2fKj.png?1 "Specify the triggered event!")

#Licence
Licenced under GPL. Some portions of the code are from wordpress itself. You should probably host this on your own server, instead of using `ifttt.captnemo.in`.

#Custom Use
Just clone the git repo to some place, and use that as the wordpress installation location in ifttt.com channel settings.

[pc]: http://partychat-hooks.appspot.com/ "Partychat Hooks"
[gh]: https://help.github.com/articles/post-receive-hooks/ "Github Post receive hooks"

#About this Fork
This is a modification of the original repo <https://github.com/mapkyca/ifttt-webhook> created by Marcus Povey <http://www.marcus-povey.co.uk>.
Since that project I've forked my own repo to modify it.
