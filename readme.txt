=== seedsugar-phphamlsass for WordPress ===
Contributors: seed&sugar AG
link: http://www.seedandsugar.de/
Tags: haml, markup, phamlp, phphaml, theme, themes, sass
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 0.1

PHPHaml for WordPress enables theme creation using the HAML template system.

== Description ==

PHAMLP for WordPress enables theme creation using the HAML template system. HAML replaces the tag soup of most HTML/PHP templates with a cleaner, hierarchical markup language.

This is a simple plugin that only parses haml and sass/scss with included php code to their respective php source and renders that.
It DOES NOT include any of rail's sophisticated templating tags, like link_to, image_tag etc..
If you want something like that you should take a look at the wordless plugin.

Any plain HTML/PHP theme will work while PHAMLP for WordPress is enabled. But the individual templates that make up a WordPress theme, from index.php to author.php, can be replaced with a corresponding .php.haml file and PHAMLP for WordPress will process them.

If enabled it will also check for sass/scss stylesheet files, compile them and replace the links in the final output.
Just link to them normally like you would to any other .css file.

The plugin parses haml and sass files into cached files in wp-content/phphaml4pw/tmp/php and wp-content/phphaml4pw/tmp/css respectively, for obvious reasons these folders need to be writable by your webserver.


As PHPHaml does not support the complete HAML syntax, take a look at https://github.com/Baldrs/phphaml for any syntax questions you might have.
Sass parser uses the parser from phamlp (http://code.google.com/p/phamlp/).

= HAML Example =

For an example HAML theme check the themes/ folder.

There are two ways you can make a theme:

a) 	The ruby way is to use layouts. For that place a layout.php.haml file in your theme which basically comprises WP's header and footer.
b) 	The WP way is to have seperate header.php and footer.php files. Since these are statically included by WP their names cannot be changed (meaning no header.haml - sorry!).
	As a workaround, I've implemented partials, just call render("header")/render("footer") in your index.php.haml and _header/_footer.php.haml will be included.
	
Content_for does not yet work - sorry!


== Installation ==

= Automated installation =

1. Log into the administrative section of your WordPress site (i.e. http://example.com/wp-admin"
1. In the "plugins" section of the menu on the left, select "add new"
1. Search for "PHPHaml4WP"
1. Install

= Manual installation =

1. Upload the `phamlp-for-wordpress` directory to the `/wp-content/plugins/` directory of your WordPress site.
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 0.1 =
* Initial version. Forked from phamlp for wordpress 0.1.

== Licenses ==

1. PHPHaml4WP a fork of Phamlp for wordpress, and is therefore available under the [ISC license](http://en.wikipedia.org/wiki/ISC_license).
1. PHPHAML's license is included in the distribution.
1. Sass Parser is from http://code.google.com/p/phamlp/ which uses the New BSD License.
1. The included themes are derived from the TwentyTen theme that ships with WordPress and inherit the original theme's GPL license.
