Changelog
==========

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

