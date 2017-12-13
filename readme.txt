=== Boxzilla ===
Contributors: Ibericode, DvanKooten, hchouhan, lapzor
Donate link: https://boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=donate-link
Tags: scroll triggered box, cta, social, pop-up, newsletter, call to action, mailchimp, contact form 7, social media, mc4wp, ibericode
Requires at least: 4.1
Tested up to: 4.9.1
Stable tag: 3.1.23
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 5.3

Flexible call to action boxes, popping up or sliding in at just the right time.

== Description ==

### Boxzilla for WordPress

Boxzilla is a *lightweight* plugin for adding flexible call-to-actions to your WordPress site. Boxes can slide or fade in at any point and can contain whatever content you like.

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

> This is the successor of the old [Scroll Triggered Boxes](https://wordpress.org/plugins/scroll-triggered-boxes/) plugin.

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

Boxzilla comes with a simple interface for customizing most box colors & borders. You can apply your own CSS by utilizing any of the following element selectors.

`
.boxzilla { } /* all boxes */
.boxzilla-5 { } /* box with ID 5 */
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


#### 3.1.23 - December 13, 2017

**Fixes**

- Event listener for hyperlinks referencing `#boxzilla-123`. We recommend using `[boxzilla_link]to generate your links though[/boxzilla_link]`. [Here's how that works](https://kb.boxzillaplugin.com/shortcode-boxzilla-link/).


#### 3.1.22 - November 20, 2017

**Fixes**

- Showing box by location hash after page load wasn't working.

**Improvements**

- Load `<script>` in box content synchronously so libraries get a chance to load before they're used.


#### 3.1.21 - October 10, 2017

**Fixes**

- Ensure that administrators can always edit boxes.


#### 3.1.20 - October 9, 2017

**Fixes**

- Screen width condition not working when using WordPress in language other than English.

**Improvements**

- Use custom capability type so access to Boxzilla boxes can be modified using a role manager plugin.


#### 3.1.19 - September 20, 2017

**Improvements**

- Trigger points based on height (scroll %, element) will now be recalculated when the page height changes.


#### 3.1.18 - September 7, 2017

**Additions**

- Added [`[boxzilla_link]` shortcode to generate the correct HTML for a link to show/toggle/hide/dismiss a box](https://kb.boxzillaplugin.com/shortcode-boxzilla-link/). 


#### 3.1.17 - August 30, 2017

**Fixes**

- IE11 issue with scroll triggered pop-ups never showing up.


#### 3.1.16 - August 2, 2017

**Fixes**

- JavaScript error when clicking `<a>` elements without `href` attributes.


#### 3.1.15 - July 26, 2017

**Additions**

- Added `[boxzilla-close]text here[/boxzilla-close]` shortcode to insert a link to close the box.
- Added setting to hide the close icon.
- Added setting to hide or show box for logged-in users.


#### 3.1.14 - July 13, 2017

**Fixes**

- IE Edge issue with sliding box animation.

**Improvements**

- Don't wait for document.ready event to initialise boxes. Fixes issues with plugins not delegaging AJAX event listeners.


#### 3.1.13 - May 11, 2017

**Improvements**

- Allow for script resources in Boxzilla box content (instead of just inline script elements).
- Reset box content when box is dismissed, eg to stop YouTube video's from playing.


#### 3.1.12 - April 24, 2017

**Improvements**

- Update endpoint URL for license API requests.


#### 3.1.11 - March 22, 2017

**Fixes**

- `#boxzilla-321` link not working when link has a nested image element.

**Improvements**

- Added the option to show on screens smaller than a certain width.
- Improved URL matching for "is url" rule.


#### 3.1.10 - March 8, 2017

**Fixes**

- Debugging statement that would log to console on every scroll event.

**Improvements**

- Reduced overlay click error margin to 40px (instead of % based).
- Minor UX improvements for "edit box" screen.


#### 3.1.9 - February 27, 2017

**Fixes**

- Compatibility error with PHP 7.1 because of function name with double underscore prefix.

**Improvements**

- Removed autofocus when box shows because of mobile browser issues & popping up keyboard.

**Additions**

- Added Romanian language files.


#### 3.1.8 - November 8, 2016

**Fixes**

- JS error on sites running HTTPS, introduced by v3.1.7.


#### 3.1.7 - November 8, 2016

**Fixes**

- Cookie length value was reset on every plugin update.
- Scroll to bottom when closing box in MobileSafari browsers.

**Improvements**

- Add CSS class to overlay when box is toggled.
- Ask for [plugin review](https://wordpress.org/support/plugin/boxzilla/reviews/#new-post) after 2 weeks of usage.


#### 3.1.6 - October 18, 2016

**Improvements**

- Failsafe against including the Boxzilla script twice, to prevent duplicate elements.


#### 3.1.5 - September 6, 2016

**Fixes**

- Box cookies were being set for _all_ boxes when dismissing a box using the overlay or ESCAPE key.
- Auto-close not working since version 3.1.3

**Improvements**

- Prevent default click event action when clicking close icon.
- Add helper classes for the [Boxzilla - Theme Pack add-on](https://boxzillaplugin.com/add-ons/theme-pack/).


#### 3.1.4 - August 24, 2016

**Fixes**

- `Boxzilla.show(123)` no longer working in previous update.


#### 3.1.3 - August 24, 2016

**Improvements**

- Don't trigger any new boxes when a box is currently open.
- Fail gracefully when not running PHP 5.3 or higher.


#### 3.1.2 - August 2, 2016

**Fixes**

- Exit-Intent not working in Safari & Firefox.


#### 3.1.1 - August 1, 2016

**Fixes**

- Scroll triggers not working in IE11.

**Improvements**

- Allow `<img>` elements inside links that open boxes.
- Better page height detection.


#### 3.1 - July 19, 2016

**Improvements**

- Completely removed jQuery dependency, resulting in better performance & smoother animations.
- Completely removed CSS file dependency.
- Box position is now visually hinted in box settings.
- Event binding improvements for [Exit Intent detection](https://boxzillaplugin.com/add-ons/exit-intent/).

**Additions**

- You can now set a cookie after the box is triggered, to prevent consecutively showing the box if a visitor does not explicitly dismiss it.


#### 3.0.3 - July 5, 2016

**Improvements**

- Add `id` attribute to box element.
- Automatically fix links in box content that use HTTP when HTTPS is needed.
- Restore global jQuery object after running user scripts, to failsafe errors.

**Additions**

- When using [MailChimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/) without AJAX, the box will now automatically re-open after reloading the page.


#### 3.0.2 - June 21, 2016

**Fixes**

- Box condition "is post" was not working with an empty value.

**Improvements**

- Prevent PHP notice when saving box without changing box rules.

**Additions**

- Added French translation files, thanks to Benoit Mercusot.


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
