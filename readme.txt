=== xili floom slideshow ===
Contributors: MS xiligroup
Donate link: http://dev.xiligroup.com/xili-floom-slideshow/
Tags: theme, floom, Post, plugin, posts, mootools, slideshow, shortcode, javascript, css
Requires at least: 2.8.0
Tested up to: 2.9
Stable tag: 0.9.0

xili-floom-slideshow integrates the floom slideshow in wordpress theme.

== Description ==

*xili-floom-slideshow integrate the Floom slideshow in wordpress theme.*

Floom slideshow designed by [Oskar Krawczyk](http://nouincolor.com/) under MIT license is wonderful and amazing. For integration inside wordpress, it can be awesome ! xili-floom-slideshow tries to install it automatically but also allows personalizations.
= How it works ? =
**xili-floom-slideshow** inserts the javascript and css file inside the header of the theme. The images attached (but not inserted) to a post (or a page) are listed for the slideshow. And after adding a `[xilifloom]` shortcode inside the content of the post, the slideshow of the images are automatically displayed.
With the dashboard Settings page, it is possible to change some properties of the slideshow without changing the original javascript: *by example, number of vertical 'venitian' blinds, speed, progress bar, visible captions, and [more](http://blog.olicio.us/2009/07/25/floom/).*
Some properties can be attached to one post by using custom fields.

**prerequisite**

* Minimun knowledges in Wordpress architecture (and css).
* If others plugins use mootools, some modifications must be done through a added function named `xilifloom_theme_header()` inside functions.php. 
* Images for a slideshow must be selected with great precaution. (Same size adapted to the frame)


== Installation ==
1. Upload the folder containing `xili-floom-slideshow.php` and other sub-folders and files to the `/wp-content/plugins/` directory.
2. Activate the plugin.
3. If you want to personalize the look and size of the frame of the slideshow, don't modify the default css and files present inside plugin's folder. Create a floom folder (and a css sub-folder) inside your current theme. Inside this, copy the floom.css, spiner.gif and frame bg image that you can adapt. xili-floom-slideshow plugin will detect automatically this folder.

= Personalizations =

Some **Custom fieds** are possible: `floom_divs` to set id of the div containing images ; `floom_captions` (true or false) to display or not the caption (title) of the images ; `floom_progressbar`  (true or false) to show (or not) the progress bar.

More infos soon...

== Frequently Asked Questions ==

= Is is possible to insert slideshow outside the content of post on CMS ? =

Yes, xili-floom-slideshow only need to find the id of the div where images are displayed (default name **blinds**).

= Is xili-floom-slideshow plugin compatible with other plugins based on Mootools ? = 

Yes, but be aware to add a special function in you functions.php. See example [here](http://dev.xiligroup.com/).

= Support Forum or contact form ? =

Effectively, prefer [forum](http://forum.dev.xiligroup.com/) to obtain some support.

== Screenshots ==

1. Settings page.
2. Folder example in current theme.
3. Blinds during transition between two images (snapshot from Oskar example).

== Changelog ==

= 0.9.0 =

* first public release

© 2009-11-19 MS dev.xiligroup.com