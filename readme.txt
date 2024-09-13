=== SuperSaaS - online appointment scheduling ===
Contributors: supersaas
Donate link: https://www.supersaas.com/
Tags: appointment scheduling, booking calendar, reservations, appointments, meetings
Requires at least: 2.7
Tested up to: 6.6
Stable tag: 2.1.11
License: GPLv2

SuperSaaS is a flexible appointment scheduling system that works with many different businesses. The basic version is free.

== Description ==

SuperSaaS is a flexible online appointment scheduling system that works with many different businesses and is available in over 28 languages. The basic version is free, a paid version is available for large users and commercial use.

The plugin can automatically log a user into a SuperSaaS schedule using his WordPress username. It passes along the user's information, creating or updating the user's information on SuperSaaS as needed. This saves users from having to log in twice.

= MORE INFORMATION =
Read the [SuperSaaS WordPress Plugin documentation page](https://www.supersaas.com/info/doc/integration/wordpress_integration) for information about how to install and setup the plugin in WordPress. Visit the [supersaas.com](https://www.supersaas.com) website for an overview of all features of the booking system.

== Installation ==

This section describes how to install the plugin and get it working.

1. Search for SuperSaaS in the automatic plugin installer, or download and unzip the plugin and upload it to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the short code [supersaas] on your pages where you want the button to appear.
1. Modify the SuperSaaS account to work with WordPress by following the [installation instructions](https://www.supersaas.com/info/doc/integration/wordpress_integration) on the SuperSaaS site.

== Frequently Asked Questions ==

= I get an "Invalid e-mail" error when I try to click the button =

Make sure you follow the [installation instructions](https://www.supersaas.com/info/doc/integration/wordpress_integration) for both the WordPress part *and the SuperSaaS* part.

== Screenshots ==

1. **Class and Events bookings** - Example of a SuperSaaS appointment schedule
2. **One-on-One Appointments** - For example used by therapists, coaches and driving instructors
3. **Multiple ways of integrating** - Integrated into a frame or as a button that opens the calendar
4. **Wordpress Plugin settings** - Configure the SuperSaaS Wordpress plugin, also see the documentation section

== Languages ==

SuperSaaS is available in over 28 languages. Check out the <a href="https://www.supersaas.com">SuperSaaS</a> website for more information.

== Changelog ==

= = 2.1.11 =
* Tested latest WordPress version 6.6

= 2.1.10 =
* Shortcode image url is sanitized

= 2.1.9 =
* Ensure that every button displayed as a result of a shortcode has a 'supersaas-confirm' class

= 2.1.8 =
* Introduce plugin configuration logging

= 2.1.7 =
* Tested latest WordPress version 6.3

= 2.1.6 =
* Fix the issue where sometimes original widget options pasted to config were ignored

= 2.1.5 =
* Add Japanese localization files

= 2.1.4 =
* Update localizations

= 2.1.3 =
* Add backward compatibility with features from version 1.x.x

= 2.1.2 =
* Restore the shortcode overrides for the schedule

= 2.1.1 =
* Hotfix for upgrading users

= 2.1 =
* Select between displaying a button and a highly customizable widget
* Choose whether to automatically log WordPress users into SuperSaaS
* Customize the widget through the shortcode
* Add helpful guidance with thorough validations on the plugin settings screen
* Tested latest WordPress version 6.2

= 2.0.8 =
* Tested latest WordPress version 6.1

= 2.0.7 =
* Tested latest WordPress version 5.9

= 2.0.6 =
* Update translation

= 2.0.5 =
* Tested latest WordPress version 5.8

= 2.0.4 =
* Tested latest WordPress version 5.6

= 2.0.3 =
* Forces the button to use https for customers still using http sites

= 2.0.2 =
* Tested latest WordPress version 5.4

= 2.0.1 =
* Change deprecated function, improve https detection

= 2.0.0 =
* Use account API Key instead of password for authentication

= 1.9.8 =
* Tested latest WordPress version

= 1.9.7 =
* More localizations and tested latest WordPress version

= 1.9.6 =
* Tested latest WordPress version

= 1.9.5 =
* Dynamically detect request protocol, it will work even with proxy TLS termination

= 1.9.4 =
* Make supersaas.com API calls use HTTPs always

= 1.9.3 =
* Bump version to support Wordpress 4.7

= 1.9.2 =
* Mark schedule name as non-optional

= 1.9.1 =
* Update translation

= 1.9 =
* Tested latest WordPress version

= 1.8 =
* Improved coding style
* Custom domain name accepts also URLs

= 1.7 =
* More localizations

= 1.6 =
* More localizations

= 1.5 =
* Added localizations
* Added a short code

= 1.0 =
* First release