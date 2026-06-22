=== Boxzilla - WordPress Popup Builder ===
Contributors: Ibericode, DvanKooten, hchouhan, lapzor
Donate link: https://boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=donate-link
Tags: popup builder, popups, slide-in, call to action, modal
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 3.4.11
License: GPL-3.0-or-later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP: 7.4

Create WordPress popup and slide-in boxes for forms, offers, notices, and calls to action

== Description ==

Boxzilla is a WordPress popup builder for adding targeted popups, slide-ins, and call-to-action boxes to your site. Show a box after a visitor scrolls, waits on the page, views multiple pages, clicks a link, or reaches a specific page element. Each box can contain any WordPress content, including shortcodes from form plugins.

Use Boxzilla for newsletter sign-up forms, content upgrades, product offers, important notices, surveys, or any other message that should appear at the right moment without taking over the whole page.

== What you can build with Boxzilla ==

* **Targeted WordPress popups** - show boxes only on the posts, pages, or conditions you choose.
* **Scroll-triggered slide-ins** - open a box after a visitor scrolls past a percentage of the page or reaches a specific element.
* **Timed call-to-action boxes** - show a message after a set number of seconds on the page.
* **Pageview-based prompts** - wait until a visitor has viewed several pages before showing a box.
* **Click-triggered modals** - open a box from a link, button, or URL hash such as `#boxzilla-94`.
* **Flexible content** - add text, images, forms, shortcodes, embeds, or custom HTML to any box.
* **Visual appearance controls** - choose the position, animation, colors, and box style from the WordPress admin.
* **Dismissal control** - decide how long a box should stay hidden after a visitor closes it.
* **Small front-end script** - Boxzilla adds only 6 kB of JavaScript to your website.

== Works with your forms and content ==

Boxzilla works with plugins that output shortcodes, including [Mailchimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/). Add the shortcode to your box content, choose when the box should appear, and publish it.

== Premium add-ons ==

The core Boxzilla plugin is free. Paid add-ons are available for advanced triggers and integrations, including exit-intent popups and time-on-site targeting.

[Browse Boxzilla add-ons](https://boxzillaplugin.com/add-ons/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description)

== Helpful links ==

* [Read more about Boxzilla](https://boxzillaplugin.com/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description)
* [View the Boxzilla demo site](https://boxzillaplugin.com/demo/#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description)
* [Read the Boxzilla Knowledge Base](https://boxzillaplugin.com/kb/)
* [Get community support on WordPress.org](https://wordpress.org/support/plugin/boxzilla)

If you are a [Boxzilla Premium customer](https://boxzillaplugin.com/pricing#utm_source=wp-plugin-repo&utm_medium=boxzilla&utm_campaign=description), use the premium support email for a faster reply.

== Installation ==

1. In your WordPress dashboard, go to Plugins > Add New.
2. Search for "Boxzilla".
3. Click Install Now, then Activate.
4. Go to Boxzilla > Add New to create your first popup or slide-in box.
5. Add your content, choose display rules, adjust the appearance settings, and publish the box.

To install manually:

1. Download the Boxzilla ZIP file from WordPress.org.
2. Go to Plugins > Add New > Upload Plugin.
3. Upload the ZIP file and click Install Now.
4. Activate the plugin.
5. Go to Boxzilla > Add New to create your first box.

Optional: install [Boxzilla add-on plugins](https://boxzillaplugin.com/add-ons/) for advanced triggers and integrations.

== Frequently Asked Questions ==

= What does this WordPress popup plugin do? =

Boxzilla lets you create popup, slide-in, modal, and call-to-action boxes for WordPress. You choose the content, page targeting, trigger, animation, and dismissal behavior.

= Can I show a form inside a Boxzilla popup? =

Yes. Boxzilla works with shortcode-based form plugins, including [Mailchimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/). Add the form shortcode to the box content and publish the box.

= Can I open a popup after someone clicks a link or button? =

Yes. Link to the box ID from any link or button. For example, if your box ID is 94, use this link:

`
<a href="#boxzilla-94">Open Box</a>
`

= Can I open a box as soon as a page loads? =

Yes. Configure this in the box settings, or add a URL hash such as `#boxzilla-13` to the page URL. Replace `13` with the ID of the box you want to show.

= Can I customize the popup design? =

Yes. Boxzilla includes appearance settings for the popup position, animation, and style. You can also add custom CSS when you need more control.

`
.boxzilla { } /* all boxes */
.boxzilla-5 { } /* only the box with ID 5 */
`

= Will Boxzilla slow down my website? =

Boxzilla adds only 6 kB of JavaScript to the front end. The script is built for small, targeted popups and slide-ins.

= How do I disable automatic paragraphs in box content? =

WordPress content filters are added to the `boxzilla_box_content` filter hook. You can remove them with this code:

`
remove_filter( 'boxzilla_box_content', 'wptexturize' );
remove_filter( 'boxzilla_box_content', 'convert_smilies' );
remove_filter( 'boxzilla_box_content', 'convert_chars' );
remove_filter( 'boxzilla_box_content', 'wpautop' );
remove_filter( 'boxzilla_box_content', 'do_shortcode' );
remove_filter( 'boxzilla_box_content', 'shortcode_unautop' );
`

= Where do I report security bugs found in this plugin? =

Please report security bugs found in the source code of the plugin through the [Patchstack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/e5e16c0c-f6c9-469f-a83e-d5342d973d88). The Patchstack team will assist you with verification, CVE assignment, and notify the developers of this plugin.


== Screenshots ==

1. A Boxzilla WordPress popup with a newsletter sign-up form.
2. A styled Boxzilla popup showing a different modal design.
3. The Boxzilla popup editor, where you manage content, triggers, targeting, and appearance settings.

== Changelog ==

= 3.4.11 =

Release date: Jun 22, 2026

- Prevent a JavaScript error when Boxzilla loads before the page body is available.
- Support opening boxes from links with deeply nested content.
- Add instructions for securely reporting plugin vulnerabilities through Patchstack.


= 3.4.10 =

Release date: Jun 01, 2026

- Add Shake animation option for boxes.
- Bundle add-on images instead of fetching extension metadata remotely.
- Prevent notices on new installations when plugin settings do not exist yet.


= 3.4.9 =

Release date: May 20, 2026

- Fix URL matching when expected URL is just a single slash.
- Fix script contents showing up as text in box content.


= 3.4.8 =

Release date: Apr 21, 2026

- Ensure nav links has href attribute before filtering.
- Normalize request URL's for URL matching. Ensures consistent trailing slash and strips tracking query parameters.
- Set cookie with expiration time matching the one for dismissing the box whenever a form inside a box is submitted.
- Remove `Boxzilla.off` from the JS API as it was a no-op and no one is using it.


= 3.4.7 =

Release date: Mar 9, 2026

- Add uninstall script to clean up all database entries from the plugin
- Fix missing closing element in box rule settings
- Fix duplicate class attribute on wrapper element on settings page
- Fix duplicate id attribute on box width input field
- Fix deprecation in call to get_terms()
- Various other type fixes, PHPDoc improvements or removal of unused code.

[View the full changelog on GitHub](https://github.com/ibericode/boxzilla-wp/blob/main/CHANGELOG.md)
