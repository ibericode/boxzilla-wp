=== Plugin Name ===
Contributors: DvanKooten, ibericode, iMazed, hchouhan
Donate link: https://scrolltriggeredboxes.com/#utm_source=wp-plugin-repo&utm_medium=scroll-triggered-boxes&utm_campaign=donate-link
Tags: scroll triggered box, cta, social, pop-up, newsletter, call to action, mailchimp, contact form 7, social media
Requires at least: 3.7
Tested up to: 4.2.2
Stable tag: 2.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Call to actions for engaged visitors, a less obtrusive alternative to pop-ups.

== Description ==

= Scroll Triggered Boxes =

**Call to action boxes which appear once a visitor has scrolled down far enough. Highly converting, less annoying!**

Scroll Triggered are boxes which slide or fade-in once a visitor has scrolled down far enough on your page.

Visitors who scroll down far enough have already engaged with your content and most likely got value out of it, making them more receptive to your offer. Plus, human eyes react to movement. Having a box slide-in at a corner of the screen is a sure way to get their attention while staying relatively unobtrusive and certainly less annoying than a pop-up.

= Any Call To Action =

Boxes can contain any content you like, even shortcodes for third-party plugins.

- Newsletter sign-up form
- Contact form
- Buttons or links
- Shortcodes
- Social sharing options
- Anything really. You decide.

= Scroll Triggered Boxes, at a glance.. =

- Unlimited amount of boxes
- Page level targeting
- Multiple animations
- Appearance control, control when and where your box should appear.
- Hide boxes for visitors who dismissed it
- Show boxes by clicking a link or button

Have a look at the [frequently asked questions](https://wordpress.org/plugins/scroll-triggered-boxes/faq/) as well, this plugin is really flexible.

**Demo**

There's a [Scroll Triggered Boxes demo site](http://demo.scrolltriggeredboxes.com/), showcasing the vast amount of possibilities for you to gather your leads.

**Add-on plugins**

There are several [premium add-on plugins available for Scroll Triggered Boxes](https://scrolltriggeredboxes.com/plugins#utm_source=wp-plugin-repo&utm_medium=scroll-triggered-boxes&utm_campaign=description), which help you get even more out of your site.

**Translations**

Please submit your new or improved translations [using a pull request on GitHub](https://github.com/ibericode/scroll-triggered-boxes).

**Bug Reports**

Bug reports for [Scroll Triggered Boxes are welcomed on GitHub](https://github.com/ibericode/scroll-triggered-boxes). Please note that GitHub is _not_ a support forum.

**Other Links**

- Have a look at some other [WordPress plugins by Danny van Kooten](https://dannyvankooten.com/wordpress-plugins/)
- Contribute to the [Scroll Triggered Boxes plugin on GitHub](https://github.com/ibericode/scroll-triggered-boxes)
- [Add-on plugins for Scroll Triggered Boxes](https://scrolltriggeredboxes.com/plugins#utm_source=wp-plugin-repo&utm_medium=scroll-triggered-boxes&utm_campaign=description)
- The [Scroll Triggered Boxes site](https://scrolltriggeredboxes.com/#utm_source=wp-plugin-repo&utm_medium=scroll-triggered-boxes&utm_campaign=description)

== Frequently Asked Questions ==

= What does this plugin do? =

Have a look at the [Scroll Triggered Boxes demo site](http://demo.scrolltriggeredboxes.com/).

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

= How do I set more advanced rules for on which pages to show a box? =

You can use [conditional tags](https://codex.wordpress.org/Conditional_Tags) to set super-customized rules.

*Example: only show for posts in category 'cars'*
`
is_single() && in_category('cars')
`

*Example: show everywhere except on pages with slug 'contact' and 'checkout'
`
!is_page( array( 'contact', 'checkout') ) 
`

= Can I have a box to open after clicking a certain link or button? =

Sure, just link to the box element.

*Example (box ID is 94 in this example)*
`
<a href="#stb-94">Open Box</a>
`

= Can I have a box to open right after opening a page? =

Sure, just include `stb-` followed by the box ID in the URL.

*Example (box ID is 94 in this example)*
`
http://your-wordpress-site.com/some-page/#stb-94
`

= How to set more advanced styling rules =

If you want more advanced styling, you can use CSS to further style the boxes. Every box gets its own unique #id as well as various CSS classes.

`
.stb-{id} { } /* 1 particular box */
.stb { } /* all boxes */
.stb-close{ } /* the close button of the box */
`

= I want to disable auto-paragraphs in the box content =

All default WordPress filters are added to the `stb_content` filter hook. If you want to remove any of them, add the respectable line to your theme its `functions.php` file.

`
remove_filter( 'stb_box_content', 'wptexturize') ;
remove_filter( 'stb_box_content', 'convert_smilies' );
remove_filter( 'stb_box_content', 'convert_chars' );
remove_filter( 'stb_box_content', 'wpautop' );
remove_filter( 'stb_box_content', 'do_shortcode' );
remove_filter( 'stb_box_content', 'shortcode_unautop' );
`

== Installation ==

= Installing the plugin =

1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for *Scroll Triggered Boxes* and click "Install now"
1. Alternatively, download the plugin and upload the contents of `scroll-triggered-boxes.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin. 

= Creating a Scroll Triggered Box =

1. Go to *Scroll Triggered Boxes > Add New*
1. Add some content to the box
1. (Optional) customize the appearance of the box by changing the *Appearance Settings*

= Additional Customization =

Have a look at the [frequently asked questions](https://wordpress.org/plugins/scroll-triggered-boxes/faq/) section for some examples of additional customization.

== Screenshots ==

1. A scroll triggered box with a newsletter sign-up form.
2. Another scroll triggered box, this time with social media sharing options.
3. A differently styled social triggered box.
4. Configuring and customizing your boxes is easy.

== Changelog ==

= 2.0.2 - May 18, 2015 =

**Fixes**

- JavaScript error when loading box editor in HTML mode
- Remove type hint for function that adds metaboxes, as this differs for new (unpublished) boxes

**Improvements**

- Output HTML for boxes at a slightly earlier hook, for better [MailChimp for WordPress](https://mc4wp.com/) compatibility.


= 2.0.1 - May 12, 2015 =

**Fixes**

- Fix page level targeting no longer working

= 2.0 - May 12, 2015 =

Major revamp of the plugin, maintaining backwards compatibility.

**Important changes**

- The plugin now comes with several [premium add-on plugins which further enhance the functionality of the plugin](https://scrolltriggeredboxes.com/plugins#utm_source=wp-plugin-repo&utm_medium=scroll-triggered-boxes&utm_campaign=changelog).
- PHP 5.3 or higher is required.
- "Test mode" is now a global setting.
- Various UX improvements.

If you encounter a bug, please [open an issue on GitHub](https://github.com/ibericode/scroll-triggered-boxes/issues).

= 1.4.4 - April 4, 2015 =

**Additions**

- Added a PHP version check in preparation for the upcoming [Scroll Triggered Boxes v2.0](https://scrolltriggeredboxes.com/a-new-site/) release.

= 1.4.3 - January 29, 2015 =

**Improvements**

- Various performance improvements
- Updated all links to use `https` protocol

= 1.4.2 - December 4, 2014 =

**Fixes**

- Box not automatically appearing if cookie time was set, caused by yesterdays update.

= 1.4.1 - December 3, 2014 =

**Fixes**

- CSS Height issue breaking SIDR navigation in some themes.

**Improvements**

- If cookie lifetime option is set to 0, existing cookies will be ignored now too.

= 1.4 - November 17, 2014 =

**Additions**

- Added option to disable box for smaller screen sizes, defaults to box width.

= 1.3.1 - September 4, 2014 =

**Bugfixes**

- Fixed an issue with rules disappearing when having more than 5 posts.

**Improvements**

- Some textual improvements.

= 1.3 - July 30, 2014 =

**Improvements**

- Various code improvements
- Minified all assets (scripts and styles)
- You can now contribute to the [Scroll Triggered Boxes plugin on GitHub](https://github.com/ibericode/scroll-triggered-boxes).

**Additions**

- Add "bottom center" and "top center" position options

= 1.2.2 - July 7, 2014 =

**Additions**

- Added Spanish translations, thanks to [Paul Benitez of Tecnofilos](http://www.tecnofilos.net/)

**Improvements**

- Now using native JS cookies, greatly reducing the script size.
- Added various debugging statements to the script.

= 1.2.1 - May 21, 2014 =

**Additions**

- You can now use JavaScript functions like `STB.show( 42 )` or `STB.hide( 42 )` to show/hide boxes.

**Improvements**

- Box is now more responsive, it will now never stretch beyond the screen width.
- Various minor code improvements.
- Wrapped remaining strings in translation calls.

= 1.2 - April 18, 2014 =
* Improved: Plugin is now fully translatable. Fixed various string typo's.

== Upgrade Notice ==

= 2.0.2 =
Minor code improvements.