Changelog
==========

#### 2.2.2 - April 11, 2016

**Fixes**

- Fixes notice on settings page when creating a new box.

**Improvements**

- Fallback for box initialization when other script errors.
- Getting ready for new Exit Intent add-on, to be released soon.
- Use event bubbling for `#stb-103` style links, so link elements loaded over AJAX can also open boxes.


#### 2.2.1 - March 2, 2016

**Fixes**

- "Test mode" setting from individual box pages not saving and throwing a warning.


#### 2.2 - March 2, 2016

**Fixes**

- CSS `initial` keyword compatibility fix for Internet Explorer

**Additions**

- Allow glob-style patterns for matching URL's and referer URL's, eg `*.google.com`.
- Allow matching any condition or all conditions to load a box.

**Improvements**

- Boxes can now be marked "unclosable" by filtering the box options (see FAQ).
- When box is "center" positioned, clicking the overlay now uses an error click margin to avoid unintentionally dismissing a box.
- Close icon can now be removed by passing an empty string to the `stb_box_close_icon` filter.
- "Test mode" setting is now shown on individual box settings pages as well, for convenience.
- When editing a box, an empty box rule is now always shown.


####  2.1.4 - November 19, 2015

**Fixes**

- Do not show box instantly if auto-show is disabled. Fixes an issue with [the premium MailChimp add-on](https://boxzillaplugin.com/add-ons/mailchimp).

####  2.1.3 - October 19, 2015 

**Fixes**

- (Non-fatal) JS error introduced in version 2.1.2

**Improvements**

- Improved error messages & general textual improvements to admin pages.

####  2.1.2 - October 15, 2015 

**Fixes**

- Sample boxes were no longer being created on plugin installation

**Improvements**

- Added "Box ID" column to boxes overview page so it's easier to find your box ID.

**Additions**

- The box cookie is now set after each form submissions, preventing it from showing up again
- When using [MailChimp for WordPress](https://mc4wp.com), the box will now auto-show again after submitting the page.

####  2.1.1 - August 20, 2015 

**Fixes**

- Activation error on Multisite.

**Additions**

- Added an "instant" option as the box trigger, which shows the box immediately after loading a page.

####  2.1 - July 8, 2015 

**Fixes**

- "If post is" filter with empty value was not working.

**Improvements**

- Added autocomplete search to filter rule values, which autocompletes post, page, category and post type slugs.
- Minor other usability improvements to box filters.

**Additions**

- Added `is_post_in_category` filter rule condition, to target posts that have a certain category.

####  2.0.4 - July 6, 2015 

**Fixes**

- Boxes were not showing if any other resource (images, scripts, etc.) on the page failed to load.

**Improvements**

- Extension thumbnails are now clickable.
- Prevent notice for empty string values in box rules.

**Additions**

- The plugin now creates a sample box upon plugin installation.

####  2.0.3 - July 2, 2015 

**Fixes**

- The cookie for closing a box was always set to expire at the end of the session

####  2.0.2 - May 18, 2015 

**Fixes**

- JavaScript error when loading box editor in HTML mode
- Remove type hint for function that adds metaboxes, as this differs for new (unpublished) boxes

**Improvements**

- Output HTML for boxes at a slightly earlier hook, for better [MailChimp for WordPress](https://mc4wp.com/) compatibility.


####  2.0.1 - May 12, 2015 

**Fixes**

- Fix page level targeting no longer working

####  2.0 - May 12, 2015 

Major revamp of the plugin, maintaining backwards compatibility.

**Important changes**

- The plugin now comes with several [premium add-on plugins which further enhance the functionality of the plugin](https://boxzillaplugin.com/add-ons/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=changelog).
- PHP 5.3 or higher is required.
- "Test mode" is now a global setting.
- Various UX improvements.

If you encounter a bug, please [open an issue on GitHub](https://github.com/ibericode/boxzilla/issues).