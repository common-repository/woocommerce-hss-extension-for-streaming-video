=== WooCommerce HSS Extension for Streaming Video ===
Author URI: https://www.hoststreamsell.com
Plugin URI: http://woo_demo.hoststreamsell.com
Contributors: hoststreamsell
Tags: sell,video,streaming,cart
Requires at least: 3.3
Tested up to: 6.1.1
Stable tag: 3.31
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The easiest and most advanced solution to selling videos with WordPress and WooCommerce

== Description ==

Get up and running in 3 easy steps!

* Sign up for a free trial account on
[HostStreamSell.com](https://www.hoststreamsell.com/?utm_source=wordpress&utm_medium=link&utm_campaign=woo_plugin)
* Upload, encode, and organize your videos
* Install WooCommerce and our WooCommerce integration plugin on your website and create all video products on your website with one click

Everything you could need!

* Rent or sell
* Stream only or streaming and download.
* Sell individual videos as well as groups of videos
* Provide multiple purchase options for the one video or video group

Demo at [woo-demo.hoststreamsell.com](https://woo-demo.hoststreamsell.com/).

More information at [HostStreamSell.com](https://hoststreamsell.com/).

== Installation ==

1. Sign up for a free trial account on
[HostStreamSell.com](https://www.hoststreamsell.com/?utm_source=wordpress&utm_medium=link&utm_campaign=woo_plugin)
2. Upload, encode, and organize your videos
3. Install WooCommerce and this plugin
 - Go to Settings > HSS WOO Admin and enter API key from your HostStreamSell account and press Save
 - Click the Update key to Pull video information from HostStreamSell platform and insert video all products into the system automatically
 - Go to WooCommerce > Settings and make sure the check box for 'Enablee guest checkout' is not checked. If it is, uncheck and press Save Changes


== Frequently Asked Questions ==

= Does this work with other video platforms =

No this only works with the HostStreamSell video platform

= How do I style the text which appears above the video player to say whether it is the trailer or full video?

Add the following to your theme's style.css to for example make the text centered

.hss_woo_watching_video_text { text-align:center; }
.hss_woo_watching_trailer_text { text-align:center; }

You can set what the text says (or whether to show any text at all through the plugin's settings)


== Changelog ==

= 0.1 =

* Initial version uploaded to WordPress. Currently only supports one price per video

= 0.2 =

* Removed some uneeded debug logging

= 0.3 =

* Add support for downloading files

= 0.4 =

* Added check if guest checkout is enabled with wanring that purchase will not work and for admin to disable this or user to make sure they register

= 0.5 =

*Betterlogic whether to show donload links depending on whether the current
user has download access

= 0.6 =

*Only add Video tab to streaming video products

= 0.7 =

*Fix jwplayer cross protocol issue between http/https

= 0.8 =

*Improve functionality around adding video access when order is in processing state. Add default log file

= 0.81 =

*Updated the readme

= 0.9 =

* added support to set a Website Reference ID. The default for this will be 0 (zero), and should only be changed in the event that you want multiple WordPress websites selling the same videos. You set a different ID for each website, which is used to distinguish for example a customer with WordPress user ID of 5 on one website, with a totally different user on another website with the same user ID of 5

= 0.91 =

*fix check-in issue

= 0.92 =

*Added ability to configure text above video when showing trailer or full video through a plugin setting

= 0.93 =

*Added some beta subtitle support functionality

= 0.94 =

*Added subtitle updates and css styling for the trailer/full video text above the video

= 1.00 =

*Updates to support responsive JW Player, and add ability to change the subtitle font size

= 1.01 =

*Minor update to admin screen checked function

= 1.02 =

*Made improvements to enable videos still be created if upload directory is not present or not writeable

= 1.03 =

*Remove spaces from API key and database ID settings when updated

= 1.04 =

*Added action for outputing content under the video if user has purchased

= 1.05 =

*Fixed PHP open tag issue

= 1.06 =

*Added functionality to create a URL on purchase which will allow access the purchased videos directly without having to log in

= 1.1 =

*Added support for variable pricing options

= 1.11 =

*Added support for selling access to a group of videos

= 1.12 =

* Fixed a bug where video groups with no thumbnail were causing an issue

= 1.13 =

* Fixed bug where video group accessnot being granted unless there were multiple purchase options

= 1.14 =

* Made group product be virtual type and also now allow letting title not be updated just like description

= 1.15 =

* Add localization support for access options

= 2.0 =

* Add support for selecting JW Player 6 or 7 *

= 2.01 =

* Fix issue with HTTPS websites *

= 2.0.2 =

* Fix issue where downloads link wasn't working when using videolink for access

= 2.0.3 =

* Fix issue when title contains apostrophe

= 2.04 =

* Fix issue with PHP 5.6

= 2.05 =

* Fix issue related to product variations

= 2.06 =

* Fix compatibility issue with old and new WooCommerce API versions related to variations

= 2.07 =

* Added logging JW Player events (Beta)

= 2.08 =

* Add support for options of same duration - will automatically use longer option names for uniqueness of option name 

= 2.09 =

* Updated loggin events code (Beta)

= 2.2 =

* Added support for premium JWPlayer and Videojs HTML players using HLS and DASH streaming protocols

= 2.21 =

* Changed default player to videojs and event loggin on

= 2.22 =

* Fixed issue with multiple purchase options

= 2.23 =

* Fixed issue with subtitles support for VTT files

= 2.24 =

* Fixed some event logging

= 2.26 =

* Added logging more html5 events for videojs player

= 2.27 =

* Added video player events for customer's purchased videos. From the Users admin screen click on the "check video usage" link for a user to see amount of bandwidth streamed as well as view details every time the play/pause/seek a video

= 2.28 =

* added Videos section to woocommerce My Account screen letting a customer view their purchased videos and play any of those videos from there

= 2.31 =

* Fixed error which broke showing video in product Video tab

= 2.40 =

* Added setting for where to display video player on product page. Added video group support to My Account Videos page

= 2.41 =

* Added ability to update Purchase option ID in woocommerce product variation admin dashboard

= 2.42 =

* Added support for https websites sending event data

= 2.43 =

* Add support to manually set hss fields in woocommerce product rather than using the auto sync

= 2.44 =

* fixed php warnings/notices

= 2.45 =

* added div classes to my account purchased video list

= 2.46 =

* changes to add support for localization

= 2.50 =

* add support for jwplayer v8

= 2.51 =

* fix/improve formating in my-account video lists

= 2.52 =

* fix/improve formating in my-account video lists

= 2.53 = 

* fix back button translation

= 2.54 =

* fix error on my account videos page

= 2.60 =

* add videojs bitrate selector and fix my-account videos not displaying for variation products

= 2.62 =

* add ability to export player events for a user - in admin dashboard click users, then for any user click show video usage link, and then click the export play details button

= 2.63 =

* support using https url of video poster for https sites

= 2.64 = 

* enable recording hss access id when access is granted so that it can be removed when an order is refunded or cancelled. My account videos page shoul only list a video once even if purchased multiple times

= 2.70 =

* enable overriding the default my account video list and video player page layouts by specifiying template files

= 2.71 =

* add HSS Video tab to edit product tabs for video settings

= 2.72 =

* fixed issue around video mappings to video groups

= 2.80 =

* add option to disable fast forward of videos

= 2.81 =

* add more info to videojs player logging for debugging playback issues

= 2.90 =

* added templates for my-account pages. These can be copied to a directory called hss-woo-templates in your theme and modified.

= 2.91 =

* fixed missing purchase ID field in product variation

= 3.01 =

* added support for videojs 7 player

= 3.02 =

* fixed issue with missing function

= 3.03 =

* added option to set products to physical instead of the default virtual

= 3.04 =

* added functionality to remember subtitle and audio track language selected by user and auto select these next time the player loads

= 3.05 =

* added global $hss_woo_user_has_access variable

= 3.06 =

* added ability to only sync new video data and not update existing videos

= 3.07 =

* when only syncing new videos do not create video categories and add new video to groups. Also fixed retaining video order as configured in HSS account group list.

= 3.10 =

* Added Chromecast support for videojs7 player. Note that this will only work when your website is using https. Subtitle or alternative audio tracks are not currently supported

= 3.11 =

* Updated tested WordPress version

= 3.21 =

* Updates for stream URL logic

= 3.22 =

* Updates for m3u8 URL logic
 
= 3.30 =

* Updates to the WooCommerce HSS Video tab
* Added meta box for Video product type to manually edit _woo_video_id

= 3.31 =

* Fixes for new _woo_video_id meta box

= 3.32 =

* Added meta box to enable video as Panorama video (VR Video)
* Added panorama support for VJS5/7 when the checkbox is checked