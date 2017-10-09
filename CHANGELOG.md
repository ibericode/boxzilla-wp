Changelog
==========

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
