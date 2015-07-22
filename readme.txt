=== Spot.IM Comments ===
Contributors: maor, BenSh
Tags: Comment, comment form, commenting, comments, comment author, comment form, comment system, comment template, comments box, community, discuss, discussion, discussions, commenter, live update, real time, realtime, real-time, Spot.IM, reply, social login, widget, social, moderation, community, communities, engagement, Facebook, profile, sharing, newsfeed, chat, chat interface, notification, notifications, SEO, retention, pageviews, email alerts, direct message, direct messaging, group message, group messaging, content, content circulation, UGC, user generated content
Requires at least: 3.8
Tested up to: 4.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Real-time comments widget turns your site into its own content-circulating ecosystem. Implement an innovative conversation UI and dynamic newsfeed to spur user engagement, growth, and retention.

== Description ==

Spot.IM Comments is an advanced, real-time commenting platform that gives website publishers direct ownership of their users’ conversation and social output. In only 5 minutes, your website can be host to a live, vivid community that lives not on Facebook or Twitter, but on your very own pages. 

Spot.IM Comments seamlessly incentivizes your users to create engaging User Generated Content through beautifully designed, real-time conversation. With a chat UI, your readers will interact with each other and hold discussions like never before. It features group and private messaging, as well as a live, smart newsfeed that directs your community members to your newest, hottest content. Spot.IM is more than a comment box. It is a commenting ecosystem centered on your, and only your, website. 

With Spot.IM, we’re on a mission to create a more involved and dedicated community on each and every site we work with. For you, the publisher, this means higher retention rates, more pageviews, and increased dwell time. Instead of allowing your content to leak out to social networks that you can’t control, we’re giving you back the power of its ownership. 

**Experience Live Commenting**
Typing users and submitted comments are seen in real time, allowing your users to produce User Generated Content like never before. There’s no waiting, no delay, and none of the frustrating load times that lead to poor site performance. Your users have less patience than ever before, and you need a system that addresses that.

**Innovative Chat Interface**
A revolutionary chat UI is integrated into the commenting experience to stimulate vibrant and instantaneous conversations. See if a user is online, and engage them in a free-flowing dialogue in either group or direct message. Hosting meaningful conversations has never been this seamless.

**Dynamic Newsfeed**
Hot topics and trending conversations are presented in a cross-site Newsfeed, driving discussion, clicks, and increased time on site for your users. The Newsfeed is specific to each site and optimized to draw your visitors in and give them a personal feel, keeping them interested and engaged with your content. It resides in an unobtrusive yet attractive button that constantly sits above-the-fold of your website, encouraging users to generate UGC before they even get to the comments section.

**Social Elements**
We’ve taken some core social functionalities and integrated them into Spot.IM’s system, giving your users the feeling of being inside a social network in your very own site. Like and share messages, tag community members in real-time with a simple “@”, share a large variety of media easily, and send and receive actionable notifications. 

**Mobile Integration**
In today’s world, mobile functionality is everything. That’s why Spot.IM is immediately optimized for all mobile platforms, ensuring that your users get just as much out of your site when they’re on the go as when they’re at home.
 

== Installation ==

1.	Install the Spot.IM Plugin either via the search option inside the WordPress plugins page (located in the toolbar of your admin page), or by uploading the files to your server (in the /wp-content/plugins/ directory). 

2.	Activate the Plugin (Install Now > Activate Plugin) 

3.	Then go to the Spot.IM Plugin Synchronize page (Spot.IM > Synchronize)  and then in the first step("choose Onwer") select the admin user.

4.  Send the exported data to support@spot.im alongside contact details.

5.	Then go to the Spot.IM Plugin settings page (Spot.IM > Settings) and fill the Spot’s ID that You have recieved from step 4.


== Screenshots ==

== Features ==

•	Live Commenting – Typing users and submitted comments are seen in real time. No waiting, no delay, no frustrating load times.
•	Chat Interface – A chat UI is integrated into the commenting experience to stimulate vibrant conversations. See if a user is online and engage in free-flowing dialogue.
•	Newsfeed – Hot topics and trending conversations are presented in a cross-site Newsfeed, driving discussion, clicks, and pageviews. Newsfeed offers smart content recommendation to your readers.
•	Direct Messaging – Community members can elect to talk privately, discuss your site’s content, and share common passions and interests.
•	Above-the-Fold – The Newsfeed can be accessed by clicking on an unobtrusive, yet attractive button that constantly sits on the side of your website. 
•	Mobile Integration
	o	Unlike other commenting platforms, Spot.IM’s mobile interface is smooth and easy to navigate, allowing your community to flourish even when it’s on the go. 
•	Notifications – Spot.IM instantly notifies your readers about new comments and hot conversations, so your content is circulated – and never missed.
•	Seamless Implementation
	o	Application of plugin within five minutes
	o	User and commenting data imported or exported with a click
	o	Automatic registration – Single Sign On (SSO) with a variety of different platforms (Facebook, LinkedIn, Google+, Twitter)
•	@Mentions – With a simple “@,” your community members can address each other within a conversation. Mentioned users get an actionable, mobile notification (still?) and an email alert even if he or she is offline. 
•	Community Interfacing – Beyond group and private messaging, users can like and share each other’s messages. 
•	Gameification – Spot.IM incentivizes your community by rewarding active contributors in your community with points and rankings. High-ranking community members push the level and flow of conversation.
•	Customization and Branding – Make your community just that – yours – with a variety of customizable design options, including colors, schemes, and icons.
•	Moderation – Advanced automatic, manual, and user moderation lets you eliminate trolls and spam, keeping your User Generated Content clean while allowing your star commenters to shine.
•	Media Sharing – Videos, images, GIFs, you name it – Spot.IM’s system supports your community in bringing a little color to the conversation. 
•	Analytics – Find out who’s clicking, where, and why to optimize and get the most out of your content. (I’d like to say more here but I’m not sure what state the analytics are in right now). 
•	Language Support – You can control your spot in 16 different languages.
•	SEO support

	For more information, please visit our website – Spot.IM 

== Frequently Asked Questions ==

Export data was finished but the donwload of exported file didn't start?
    
    Check the permission of the file /plugin_dir/sample-data/export.json or copy export text from textarea section on the fininsh step.

If I don't set Spot’s ID what will happen?
	
	The standart comment form will be used.

How I can get Spot’s ID?
   
    You can get it sending them exported data to support@spot.im alongside contact details.

If I change Spot’s ID or selected owner what will be happen?

   Spot’s comments won't show your site comments that was saved by old Spot’s ID, only new comments will be shown.

How I can change the selected owner?

   You can remove the plugin and install it again.
   
Export data stop at X(any number)%?
   
    It happens when you have too many comments and memory of the server does not lack. Contact with your hosting provider support and ask them to increase php memory limit. Or you if you have PHP knowledge you can put code "define( 'WP_MEMORY_LIMIT', '256M' );" in your wp-config.php file.
	You can read more here https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP
	
Which browsers will support my Spot?
	
	All popular browsers support Spot! (Note: Need to consult developers on this)

How do I moderate content on my Spot?

	Spot.IM offers several moderation tools and options, all of which are either automatic, manual, or user-based. You can find all the information about our moderation tools here and also contact us at support@spot.im for any help, offers, or suggestions.  

How do I access Spot analytics?

	This feature, coming soon, will be available on our Manage page, which is also linked on the WordPress dashboard under the Spot.IM Plugin page. 

How does the Newsfeed work? Can I control what’s on it?
	
	The Newsfeed works through an algorithm that determines what trending and recent content on your website your users need to see. If you have questions about how that works in more depth, please contact us at support@spot.im.


What is SEO and does Spot support it? 

	SEO stands for Search Engine Optimization. When you’re asking if Spot.IM has SEO, you’re asking if Spot allows all of your User Generated Content (UGC) to be indexed on your site so that a search engine like Google, or Bing, can read it. 

	The answer to that question is yes! Spot.IM is SEO-compatible. Unlike other popular commenting systems like Disqus or Facebook Comments, Spot.IM not only allows your UGC to be indexed and searched, it indexes that content only on your site itself. Other commenting systems use their own 3rd party destination websites to index your UGC, which means that when keywords are searched, their content comes up, and not yours. 

	With Spot.IM, your content is your content, no matter if its an article you wrote or a user conversation in the comment section. This gives your website improved visibility in every way. 


How do I get mobile working for my Spot?

	Spot.IM is automatically mobile web compatible. All you need to do is set it up for your WordPress website using the Plugin instructions above, and you’ll have a beautiful, responsive, slick mobile commenting and Newsfeed interface that works on all phones and tablets.

	No extra work necessary!


How do I change the language of my Spot’s interface?

	Currently the Spot’s interface is partially translated into 16 languages: English, German, Spanish, French, Hebrew, Hungarian, Italian, Japanese, Korean, Dutch, Portuguese, Romanian, Russian, Turkish, Ukrainian and Chinese. Soon to be fully translated.

	The language of the user’s browser determines the language of the interface: If a user is using a browser in French, they will see the translated interface in French, etc.

	Is spam filtered?
	Yes. To go along with other moderation tools, Spot.IM offers extensive word filter, blacklist, and spam control tools. 


How much does it cost?

	Spot.IM is free! We care deeply about returning the power of content to publishers, and we want our product’s pricing to reflect that.

	
How do I update my Spot?

	Like with other WordPress plugins, updating your Spot is easy. When an update is available, you’ll receive a notification on your site’s WordPress dashboard. From that dashboard, you’ll be able to quickly and easily update Spot.


Where can I see a Spot working live to get a better idea of it?

	You can see some great live Spots at:
	www.cgsociety.org
	www.fightofthenight.com
	More?

	Are there any tips or tricks I can use to help make my Spot – and my community – more vibrant?

	Why, how funny you should ask: there are! We strongly recommend you take a look at our Best Practices page to get a better idea o how to best utilize your Spot. A Spot is a versatile asset for your website, and you’ll want to manage it however best fits your specific needs. 


	My question wasn’t answered here. Where can I get further support?

	We know managing a website can get complicated. That’s why we made Spot.IM easy to use, even with its innovation. If you still need help with anything, try our very own Spot.Im Community site. From there, you can examine our Knoweldge Base, our Blog, and more.

	You are also always more than welcome to contact our team at support@spot.im. We’ll be glad to help.


== Changelog ==

= 1.0.2 =
* Launch