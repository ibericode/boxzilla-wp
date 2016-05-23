=== Boxzilla ===
Contributors: Ibericode, DvanKooten, hchouhan, lapzor
Donate link: https://boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=donate-link
Tags: scroll triggered box, cta, social, pop-up, newsletter, call to action, mailchimp, contact form 7, social media,mc4wp
Requires at least: 3.8
Tested up to: 4.5.2
Stable tag: 3.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Flexible call to action boxes, popping up or sliding in at just the right time.

== Description ==

### Boxzilla for WordPress

Boxzilla is a *lightweight* plugin for adding flexible call-to-actions to your WordPress site. Boxes can slide or fade in at any point and can contain whatever content you like.

> This is the successor of the old [Scroll Triggered Boxes](https://wordpress.org/plugins/scroll-triggered-boxes/) plugin.

#### Some of Boxzilla's features

- Boxes can contain _any_ content you like.
- Various box triggers:
    - Scroll percentage
    - Reaching a certain page element
    - X amount of time on the page
    - Exit Intent (premium)
    - Time on Site (premium)
    - Manually by clicking a link or button
- Customizable box position on the screen.
- Various visibility animations.
- Advanced page targeting.
- Full control over how long (and whether) boxes should stay hidden.
- Customizable box appearance using a simple & intuitive interface.
- Mobile optimized.

[Read more about Boxzilla](https://boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description).

#### Documentation

Please have a look at the [Boxzilla KB](https://kb.boxzillaplugin.com/).

#### Demo

There's a [Boxzilla demo](https://demo.boxzillaplugin.com#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description) with some examples.

#### Add-on plugins

The core Boxzilla plugin is and always will be free. Additional advanced functionality is available through several add-ons. Not only do they extend the core functionality of the plugin, they also help to fund further development of the core (free) plugin.

[Browse available add-ons for Boxzilla](https://boxzillaplugin.com/add-ons/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description).

#### Contributing and reporting bugs=

You can contribute to [Boxzilla on GitHub](https://github.com/ibericode/boxzilla).

#### Support

Please use the [WordPress.org plugin support forums](https://wordpress.org/support/plugin/boxzilla) for community support where we try to help all users.

If you think you've found a bug, please [report it on GitHub](https://github.com/ibericode/boxzilla/issues).

If you're on [one of the available premium plans](https://boxzillaplugin.com/pricing#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description), please use the support email for a faster reply.

== Frequently Asked Questions ==

= What does this plugin do? =

Have a look at the [Boxzilla demo site](https://demo.boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description).

= How to display a form in the box? =

Boxzilla will work with any plugin that offers shortcodes, like [MailChimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/).

= Can I have a box open after clicking a certain link or button? =

Sure, by linking to the box element.

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

= Can I customize the appearance of a box =

Boxzilla comes with a simple interface for customizing most colors & borders of the box, but you're in no way limited to apply your own CSS rules.

`
.boxzilla-{id} { } /* 1 particular box */
.boxzilla { } /* all boxes */
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

= I want to make it impossible to close a box =
`
add_filter( 'boxzilla_box_options', function( $opts, $box ) {
	$opts['closable'] = false;
	return $opts;
}, 10, 2 );
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


#### 3.0.1 - May 23, 2016

**Improvements**

- You can now use `<script>` tags directly in box content (again).

**Additions**

- Add "post tag is / is not" loading condition.
- Plugin will now show a notice to deactivate old Scroll Triggered Boxes plugin.
- Internal changes to dependency container for use in add-on plugins.
- Add-on: [Boxzilla Pageviews Trigger](https://boxzillaplugin.com/add-ons/pageviews/).
- Add-on: [Boxzilla WooCommerce](https://boxzillaplugin.com/add-ons/woocommerce/)


#### 3.0 - May 11, 2016

Initial release of [Boxzilla](https://boxzillaplugin.com/), formerly known as [Scroll Triggered Boxes](https://wordpress.org/plugins/scroll-triggered-boxes/).

If you're upgrading from the old plugin, please check [updating to Boxzilla from Scroll Triggered Boxes](https://kb.boxzillaplugin.com/updating-from-scroll-triggered-boxes/) for a list of changes you should be aware of.


== Upgrade Notice ==

= 2.1 =
Added autocomplete to box filters & minor bux fixes for filter rules.
