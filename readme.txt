=== Boxzilla ===
Contributors: Ibericode, DvanKooten, hchouhan, lapzor
Donate link: https://boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=donate-link
Tags: pop-up, pop-ups, modal, call to action
Requires at least: 4.6
Tested up to: 6.8
Stable tag: 3.4.1
License: GPL-3.0-or-later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP: 7.4

Flexible pop-ups or slide-ins, showing up at just the right time.

== Description ==

### Boxzilla for WordPress

Boxzilla is a plugin allowing you to add pop-up or slide-in boxes to your WordPress site. Boxes can slide or fade in at any point and can contain whatever content you like.

#### Some of Boxzilla's features

- Boxes can contain any content you like.
- You can show boxes at various events:
	- After scrolling down past a certain percentage of the page height.
	- After scrolling down to a specific page element.
	- After X seconds on the page.
	- After X pageviews on the site.
	- Manually by clicking a link or button
	- When the user intents to exit your website (Premium)
	- After X seconds on the site (any page). (Premium)
- Customizable box position on the screen.
- Animations for showing or hiding boxes.
- Page targeting, to only show pop-ups on certain posts or pages.
- Full control over how long boxes should stay hidden once they are dismissed.
- Customizable box appearance using a simple visual interface.
- Only 6 kB of JavaScript is added to your website.

[Read more about Boxzilla](https://boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description).

> #### Premium add-ons for Boxzilla
>
> The Boxzilla plugin itself is entirely free. Advanced functionality is available through several paid add-ons. Not only do they extend the core functionality of the plugin, they also help to fund further development of the core (free) plugin.
>
> [Browse available add-ons for Boxzilla](https://boxzillaplugin.com/add-ons/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description).

#### Documentation

Please have a look at the [Boxzilla Knowledge Base](https://kb.boxzillaplugin.com/).

#### Demo

There's a [Boxzilla demo site](https://demo.boxzillaplugin.com#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description) with some examples.

#### Support

Please use the [WordPress.org plugin support forums](https://wordpress.org/support/plugin/boxzilla) for community support.

If you're a [Boxzilla Premium customer](https://boxzillaplugin.com/pricing#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description), please use our support email for a faster reply.

== Frequently Asked Questions ==

= What does this plugin do? =

Pop-ups. Have a look at the [Boxzilla demo site](https://demo.boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description).

= How to display a form in the box? =

Boxzilla will work with any plugin that allows for shortcodes, like [MailChimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/) or [HTML Forms](https://wordpress.org/plugins/html-forms/).

= Can I have a box open after clicking a certain link or button? =

Yes, you can create a link that refers the box ID. For example, if your box has an ID of 94.

`
<a href="#boxzilla-94">Open Box</a>
`

= Can I have a box open right after loading a page? =

You can configure this in the box settings. Or you can add `#boxzilla-13` to your URL, where 13 is the ID of the box you want to show.

= Can I customize the appearance of a box =

Boxzilla comes with a simple interface for customizing the pop-up appearance. But you can also use your own custom CSS if needed.

`
.boxzilla { } /* all boxes */
.boxzilla-5 { } /* only the box with ID 5 */
`

= I want to disable auto-paragraphs in the box content =

All default WordPress filters are added to the `boxzilla_box_content` filter hook. The example below shows how to remove them.

`
remove_filter( 'boxzilla_box_content', 'wptexturize') ;
remove_filter( 'boxzilla_box_content', 'convert_smilies' );
remove_filter( 'boxzilla_box_content', 'convert_chars' );
remove_filter( 'boxzilla_box_content', 'wpautop' );
remove_filter( 'boxzilla_box_content', 'do_shortcode' );
remove_filter( 'boxzilla_box_content', 'shortcode_unautop' );
`

== Installation ==

= Installing the plugin =

1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for *Boxzilla* and click "Install now"
1. Alternatively, download the plugin and upload the contents of `boxzilla.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin.
1. (Optional) Install [add-on plugins for Boxzilla](https://boxzillaplugin.com/add-ons/)

= Creating a Boxzilla box =

1. Go to *Boxzilla > Add New*
1. Add some content to the box
1. (Optional) customize the appearance of the box by changing the *Appearance Settings*

= Additional Customization =

Have a look at the [frequently asked questions](https://wordpress.org/plugins/boxzilla/faq/) section for some examples of additional customization.

== Screenshots ==

1. A Boxzilla pop-up with a newsletter sign-up form.
3. Another Boxzilla pop-up, with different styles.
4. Screenshot of the page to manage a Boxzilla pop-up.

== Changelog ==


### 3.4.1 - Mar 24, 2025

- Bump required PHP version to 7.4 or higher.
- Initialize boxes at document.DOMContentLoaded instead of window.load event.


#### 3.4.0 - Jan 03, 2025 

- Remove deprecated Bootstrapper class.
- Add link to https://my.boxzillaplugin.com when trying to activate an expired license.
- Bump tested WordPress version.


#### 3.3.3 - Oct 07, 2024

- Fix unclosed element on plugins overview page, breaking the table layout.
- Bump license to GPL v3 or higher.
- Bump required PHP version to 7.2 or higher.


#### 3.3.2 - Oct 02, 2024

- The Boxzilla script itself is now deferred for improved client-side performance.
- You can now use the Boxzilla JS API before the script has fully loaded. 
- Minor server side performance improvements by getting rid of unnecessary sprintf calls or string copies.
- All i18n function call return values are now HTML escaped.
- Verify `Jetpack::is_module_active` exists before method is called.


#### 3.3.1 - May 17, 2024

- Minor performance improvements to plugin bootstrapping and autoloader implementation.
- Fix link in license key form.
- Move CSS out of JS file for improved parsing performance.
- Move to webpack instead of Gulp for preparing client-side asset files. 
- Bump required PHP version to 7.0 or later.


#### 3.2.27 - Mar 28, 2023

- Fix scroll based trigger regression introduced in previous version.


#### 3.2.26 - Mar 21, 2023 

- Check if `administrator` role exists before adding capabilities to it. Thanks [Jaime Martínez](https://jaimemartinez.nl/).
- Fix "undefined array key href" warning on PHP8.
- Fix PHP8.1 deprecation warnings.


#### 3.2.25 - Apr 20, 2021

- Change usage of deprecated jQuery.load method.
- Add `aria-modal="true"` to overlay element.


#### 3.2.24 - Nov 3, 2020

- Allow for `#boxzilla-ID` links in `<area>` elements.
- Show certain settings even if no trigger is chosen.
- Only show auto-hide setting if trigger is set to element or percentage (ie trigger condition can revert).


#### 3.2.23 - Jul 13, 2020

- Add `aria-label` to close icon to help screen readers.


#### 3.2.22 - Mar 19, 2020

- Minor code improvements
- Check if body element exists before updating class attribute, fixes an issue with some page builders.


#### 3.2.21 - Feb 18, 2020

- "If post category" or "if post tag" conditionals now apply to any post-type using built-in WP categories or tags.


#### 3.2.20 - Jan 20, 2020

**Fixes**

- An issue with the "pageviews" trigger on Safari Mobile where session storage is inaccessible in the beforeunload event.

**Improvements**

- Prepare for upcoming [Mailchimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/) plugin update which changes the name of the JS object when a form is submitted without AJAX.


#### 3.2.19 - Dec 30, 2019

**Fixes**

- Box rules using "contains" would only check first argument (when using comma-separated value).

**Improvements**

- Use a dedicated overlay element per box to prevent issues with multiple boxs showing on a page. Thanks Jason Maurer!


#### 3.2.18 - Dec 2, 2019

**Fixes**

- Missing quotes in HTML attribute on "edit box" page.


#### 3.2.17 - Nov 18, 2019

**Fixes**

- Notices when checking for updating and not having some add-on plugins installed.



#### 3.2.16 - Nov 15, 2019

**Improvements**

Roll-back a change in version 3.2.15 that caused an issue with Boxzilla Theme Pack and Boxzilla WooCommerce.

Please make sure your [Boxzilla plugin license](https://my.boxzillaplugin.com/) is activated and then update Boxzilla Theme Pack and Boxzilla WooCommerce to the latest version.


#### 3.2.15 - Nov 6, 2019

**Improvements**

- Add proper SVG icon with neutral color for admin menu.
- Use Page Visibility API for time-based triggers (time on site & time on page).
- Stop using `supress_filters` when retrieving boxes for a possible performance improvement.
- Minor performance improvement in bootstrapping logic.
- Add link to [Koko Analytics](https://wordpress.org/plugins/koko-analytics/)


#### 3.2.14 - Aug 7, 2019

**Fixes**

- Issue with incorrect argument count for some sites with custom menu's.


#### 3.2.13 - Aug 5, 2019

**Improvements**

- Allow more query hash parameters for opening a box.
- Allow bypassing animation for opening or closing boxes.

**Additions**

- Easily link to boxes from WP Menu's.


#### 3.2.12 - June 7, 2019

**Improvements**

- Allow skipping animations when showing, hiding or dismissing a box.
- Check for empty box content after running filter hooks, instead of before.


#### 3.2.11 - May 8, 2019

**Improvements**

- Update loading configuration when duplicating a box.
- Accept query parameters in URL hash for opening a box through a link click or on loading a page.


#### 3.2.10 - February 15, 2019

**Improvements**

- Better [exit intent detection](https://boxzillaplugin.com/add-ons/exit-intent/) for mobile devices.

**Additions**

- New bulk action to quickly duplicate a box with all of its settings.


#### 3.2.9 - December 5, 2018

**Improvements**

- Make sure preview updates with correct color values when applying box styles.
- Use small margin of error to prevent iOS scroll bounce from closing box again.


#### 3.2.7 - July 31, 2018

**Fixes**

- Issue with boxes with only an iframe, image or video and no text not being loaded.


#### 3.2.6 - June 27, 2018

**Improvements**

- Show and/or between rules to help clarify rule logic.
- Consistent line endings in main plugin file.


#### 3.2.5 - June 6, 2018

**Fixes**

- Some JSON encoders would print Boxzilla config as object, resulting in no boxes actually being loaded.

**Additions**

- Add "does not contain" qualifier in URL and referrer conditions.



#### 3.2.4 - May 31, 2018

**Fixes**

- Boxzilla content replicating parts of the page if other plugins "incorrectly" use `the_content` filter.


#### 3.2.3 - May 29, 2018

**Improvements**

- Allow "contains" qualifier in URL and referrer conditions.
- Include query string in URL conditions.
- Use SVG for the menu ico. Thanks [Kurt Zenisek](https://github.com/KZeni)
- Added Czech translations. Thanks [Zdenek Petrbok](https://petrbok.cz/)
- Run the_content filter on Boxzilla post content, to enable plugins like Photon.
- Ensure content element exists when initialising Boxzilla.
- Improvements to licensing related code for [Boxzilla Premium](https://boxzillaplugin.com/pricing/) users.


#### 3.2.2 - March 12, 2018

**Improvements**

- Print box contents at an earlier footer hook, so it works with "smart enqueue" methods in other plugins like Mailchimp for WordPress or Maxbuttons.


#### 3.2.1 - March 5, 2018

**Fixes**

- Can't use return value in write context error, introduced in v3.2.



#### 3.2 - March 5, 2018

**Fixes**

- Compatibility with plugins that use JavaScript rendering.

**Improvements**

- Skip boxes with empty content.


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

- When using [Mailchimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/) without AJAX, the box will now automatically re-open after reloading the page.


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
