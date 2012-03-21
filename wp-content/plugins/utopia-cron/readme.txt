=== Utopia Cron ===

Contributors: activeblogging
Donate link: http://activeblogging.com/member-benefits/
Tags: plugin, cron, admin, control, notification
Requires at least: 2.3
Tested up to: 2.5
Stable tag: trunk

Makes it easy to set up (semi)-timed page load events without messing with cron or other third-party timing scripts.

== Description ==

Replacement for third-party timing scripts (cron) for timing events. Enter URLs and time intervals to get the URLs loaded at about those times. Useful for scripts that need a specific URL called to 'manage' the script.

== Installation ==

1. Upload 'utopia38.php' to your '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Manage/U-Cron subtab for settings after activating

== Frequently Asked Questions ==

* What does this do exactly?

Some scripts have timing or cleanup processes (such as feed or RSS plugins), and the script writer has provided a page that needs to be loaded to 'run' these. Rather than trying to load the page in your browser at regular intervals, enter the URL here and the page will be loaded from WordPress.

* Is the timing exact?

No. It uses blog visitor page views to do the timing, so on a blog that's rarely visited, it may not be called often enough. As well, if there are a lot of URLs to visit in your list, there may not be time enough to reach them all. In this case, enter fewer URLs, or call them less often.

* Where can I read more?

Visit http://ActiveBlogging.com/info/wordpress-cron-plugin/




