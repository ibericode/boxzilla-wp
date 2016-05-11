=== Boxzilla ===
Contributors: Ibericode, DvanKooten, hchouhan, lapzor
Donate link: https://boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=donate-link
Tags: scroll triggered box, cta, social, pop-up, newsletter, call to action, mailchimp, contact form 7, social media,mc4wp
Requires at least: 3.8
Tested up to: 4.5.2
Stable tag: 3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Flexible call to action boxes, popping up or sliding in at just the right time.

== Description ==

= Call-To-Action Boxes =

Boxzilla is a *lightweight* plugin for adding flexible call-to-actions to your WordPress site. Boxes can slide or fade in at any point and can contain whatever content you like.

= Features =

- Create boxes containing whatever content you like: shortcodes, links, custom HTML, anything really.
- Show boxes automatically after scrolling down based on a percentage point or a certain element (like your comment section).
- Show boxes using a button or link.
- Choose the box position: centered or in any corner of the screen.
- Choose between a fading or sliding animation for showing the box.
- Customize the box appearance using a few simple color & dimension controls.
- Only load the box on certain pages, posts, etc.
- Control how long dismissed boxes should stay hidden.
- Control whether boxes should show on small screens.

[Read more about Boxzilla](https://boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description).

= Documentation =

Please have a look at the [frequently asked questions](https://wordpress.org/plugins/boxzilla/faq/).

= Demo =

There's a [Boxzilla demo site](https://demo.boxzillaplugin.com#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description), showcasing the vast amount of possibilities for you to gather your leads.

= Add-ons =

The core Boxzilla plugin is free and always will be. Additional advanced functionality is available through several add-ons. Not only do they extend the core functionality of the plugin, they also help to fund further development of the core (free) plugin.

[Browse available add-ons for Boxzilla](https://boxzillaplugin.com/add-ons/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description).

Some popular add-ons include:

**[Theme Pack](https://boxzillaplugin.com/add-ons/theme-pack#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description)**

A set of beautiful plug & play themes which make your boxes really stand out.

**[MailChimp for WordPress](https://mc4wp.com/)**

An easy way to show a MailChimp sign-up form inside your boxes.

= Contributing and reporting bugs =

You can contribute to this plugin using GitHub: [ibericode/boxzilla](https://github.com/ibericode/boxzilla)

= Support =

Please use the [WordPress.org plugin support forums](https://wordpress.org/support/plugin/boxzilla) for community support where we try to help all users.

If you think you've found a bug, please [report it on GitHub](https://github.com/ibericode/boxzilla/issues).

If you're on [one of the available premium plans](https://boxzillaplugin.com/pricing#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description), please use the support email for a guaranteed & faster response.

== Frequently Asked Questions ==

= What does this plugin do? =

Have a look at the [Boxzilla demo site](https://demo.boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description).

= How to display a form in the box? =

The plugin is battle-tested with the plugins below but will work with any plugin that uses shortcodes.

- [MailChimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/)
- [Contact Form 7](https://wordpress.org/plugins/contact-form-7/)

= How to display sharing options in the box? =

The plugin is tested with the plugins below but will work with any plugin that uses shortcodes.

- [Social Sharing By Danny](https://wordpress.org/plugins/dvk-social-sharing/)
- [Shareaholic](https://wordpress.org/plugins/shareaholic/)
- [Social Media Feather](https://wordpress.org/plugins/social-media-feather/)
- [WP Socializer](https://wordpress.org/plugins/wp-socializer/)
- [Tweet, Like, Google +1 and Share](https://wordpress.org/plugins/only-tweet-like-share-and-google-1/)

= Can I have a box to open after clicking a certain link or button? =

Sure, just link to the box element.

*Example (box ID is 94 in this example)*
`
<a href="#boxzilla-94">Open Box</a>
`

= Can I have a box to open right after opening a page? =

Sure, just include `boxzilla-` followed by the box ID in the URL.

*Example (box ID is 94 in this example)*
`
http://your-wordpress-site.com/some-page/#boxzilla-94
`

= How to set more advanced styling rules =

If you want more advanced styling, you can use CSS to further style the boxes. Every box gets its own unique #id as well as various CSS classes.

`
.boxzilla-{id} { } /* 1 particular box */
.boxzilla { } /* all boxes */
.boxzilla-close{ } /* the close button of the box */
`

= I want to disable auto-paragraphs in the box content =

All default WordPress filters are added to the `stb_content` filter hook. If you want to remove any of them, add the respectable line to your theme its `functions.php` file.

`
remove_filter( 'boxzilla_box_content', 'wptexturize') ;
remove_filter( 'boxzilla_box_content', 'convert_smilies' );
remove_filter( 'boxzilla_box_content', 'convert_chars' );
remove_filter( 'boxzilla_box_content', 'wpautop' );
remove_filter( 'boxzilla_box_content', 'do_shortcode' );
remove_filter( 'boxzilla_box_content', 'shortcode_unautop' );
`

= I want to disable closing of the box =
`
add_filter( 'boxzilla_box_options', function( $opts ) {
	$opts['closable'] = false;
	return $opts;
});
`

== Installation ==

= Installing the plugin =

1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for *Boxzilla* and click "Install now"
1. Alternatively, download the plugin and upload the contents of `boxzilla.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin. 

= Creating a Boxzilla box =

1. Go to *Boxzilla > Add New*
1. Add some content to the box
1. (Optional) customize the appearance of the box by changing the *Appearance Settings*

= Additional Customization =

Have a look at the [frequently asked questions](https://wordpress.org/plugins/boxzilla/faq/) section for some examples of additional customization.

== Screenshots ==

1. A scroll triggered box with a newsletter sign-up form.
2. Another scroll triggered box, this time with social media sharing options.
3. A differently styled social triggered box.
4. Configuring and customizing your boxes is easy.

== Changelog ==


#### 3.0 - May 11, 2016

Initial release of [Boxzilla](https://boxzillaplugin.com/), formerly known as [Scroll Triggered Boxes](https://wordpress.org/plugins/scroll-triggered-boxes/).

If you're upgrading from the old plugin, please check [updating to Boxzilla from Scroll Triggered Boxes](https://kb.boxzillaplugin.com/updating-from-scroll-triggered-boxes/) for a list of changes you should be aware of.


== Upgrade Notice ==

= 2.1 =
Added autocomplete to box filters & minor bux fixes for filter rules.
