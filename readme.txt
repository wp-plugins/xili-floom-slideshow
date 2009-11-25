=== xili floom slideshow ===
Contributors: MS xiligroup
Donate link: http://dev.xiligroup.com/xili-floom-slideshow/
Tags: theme, floom, Post, plugin, posts, mootools, slideshow, shortcode, javascript, css, iPhone, gallery
Requires at least: 2.8.0
Tested up to: 2.9
Stable tag: 0.9.2

xili-floom-slideshow integrates the floom slideshow in wordpress theme.

== Description ==

*xili-floom-slideshow integrates the Floom slideshow in wordpress theme.*

Floom slideshow designed by [Oskar Krawczyk](http://nouincolor.com/) under MIT license is wonderful and amazing. For integration inside wordpress, it can be awesome ! xili-floom-slideshow tries to install it automatically but also allows personalizations.
= How it works ? =
**xili-floom-slideshow** inserts the javascript and css file inside the header of the theme. The images attached (but not inserted) to a post (or a page) are listed for the slideshow. And after adding a `[xilifloom]` shortcode inside the content of the post, the slideshow of the images of the gallery are automatically displayed.
With the dashboard Settings page, it is possible to change some properties of the slideshow without changing the original javascript: *by example, number of vertical 'venitian' blinds, speed, progress bar, visible captions, and [more](http://blog.olicio.us/2009/07/25/floom/).*
Some properties can be attached to one post by using custom fields.

= new 0.9.2 =

* add for pictures order by menu_order. If order is set in gallery linked to a post, displayed series is ordered by these numbers ascendant.
* more parameters.
* add hooks and filters : to allow better selection of floom values and choice of series of images (not necessary attached to a post) according the theme or cms architecture, two filters was added : `xili_floom_get_values` and `xili_floom_get_images` insertable in functions inside `functions.php` of the current theme. Very useful to personalize header according place inside the site architecture. [example](http://dev.xiligroup.com/?cat=480&lang=en_us) 

= prerequisite =

* Minimun knowledges in Wordpress architecture (and css).
* If others plugins use mootools, some modifications must be done through a added function named `xilifloom_theme_header()` inside functions.php. 
* Images for a slideshow must be selected with great precaution. (Same size adapted to the frame)


== Installation ==
1. Upload the folder containing `xili-floom-slideshow.php` and other sub-folders and files to the `/wp-content/plugins/` directory.
2. Activate the plugin.
3. If you want to personalize the look and size of the frame of the slideshow, don't modify the default css and files present inside plugin's folder. Create a floom folder (and a css sub-folder) inside your current theme. Inside this, copy the floom.css, spiner.gif and frame bg image that you can adapt. xili-floom-slideshow plugin will detect automatically this folder.

= Personalizations =

Some **Custom fieds** are possible: `floom_divs` to set id of the div containing images ; `floom_captions` (true or false) to display or not the caption (title) of the images ; `floom_progressbar`  (true or false) to show (or not) the progress bar. `floom_title_desc` is set by default to 1 and display the title of the attached images. (2 : only the description and 3 : both title and description).

* `floom_parentID` to choose attachment from another post (and not the current post where slideshow in inserted)
* `floom_subname` to sub-select a series of pictures attached by set a mask. [See post](http://dev.xiligroup.com/?p=1269).
* `floom_container` to inform name of container (default : blinds-cont).
* `floom_display` to decide if the container is displayed when no image (block or none) - default : block.

The following custom fields (prefix floom_) work like javascript parameters. Use them with prudence..

* `floom_amount` (number of blinds)
* `floom_interval` (interval between change)
* `floom_axis` (by default : vertical)

It is also possible to fireEvent (onSlideChange and onPreload) by choosing name of fired functions (javascript added by functions in current theme).

= xilitheme-select plugin compatibility (for iPhone) =
As in website [dev.xiligroup.com](http://dev.xiligroup.com/), it is now possible to specify a floom.css in each theme (the for desktop, the for mobile as iPhone or iPod).

More detailled infos soon...

== Frequently Asked Questions ==

= Is it possible to insert slideshow outside the content of post on CMS ? =

Yes, xili-floom-slideshow only need to find the id of the div where images are displayed (default name **blinds**).

= What happen when iPhone or iPod visit the website ?
As you know, flash is not compatible with iPhone, but javascript and Floom is !
If xilitheme-select plugin is activated, the theme for iPhone is selected and the floom.css inside this theme is choosen. [see snapshot](http://wordpress.org/extend/plugins/xili-floom-slideshow/screenshots/).

= Is xili-floom-slideshow plugin compatible with other plugins based on Mootools ? = 

Yes, but be aware to add a special function in you functions.php. See example [here](http://dev.xiligroup.com/).

= Support Forum or contact form ? =

Effectively, prefer [forum](http://forum.dev.xiligroup.com/) to have support (with delay around one or two days - here European Time).

== Screenshots ==

1. Settings page.
2. Folder example in current theme.
3. Blinds during transition between two images (snapshot from Oskar example).
4. Example in iPhone Safari.
5. Ordered list of images.

== Changelog ==

= 0.9.2 =

* add for pictures orderby menu_order 
* add hooks and filter
* add container and display params
* fixes (quoted captions)

= 0.9.1 =

* xilitheme-select plugin compatibility (for iPhone), 
* add options to fireEvents, 
* fixes,...

= 0.9.0 =

* first public release

© 2009-11-25 MS dev.xiligroup.com