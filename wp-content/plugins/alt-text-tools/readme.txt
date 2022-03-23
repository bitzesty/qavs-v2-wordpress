=== Alt Text Tools ===
Contributors: eatingrules, andreykal
Author URI: https://www.nerdpress.net/
Tested Up To: 5.6
Stable tag: trunk
Tags: alt text, accessibility, alternative text, alt tags, csv export, image alt, fix images, alt text tools
License: GPLv2

Find and fix missing Alt Text quickly and easily! Exports a CSV file of all images (and their Alt Text attributes) that are actually used in your content.

== Description ==

Find and fix missing Alt Text quickly and easily!

This plugin will provide a CSV (comma separated values) file that lists all of your images used in your content -- and their corresponding Alt attribute.

To use, go to `Tools > Alt Text Tools`, and click the button. Give it a few moments to scan your site, and your download should begin. 

You can then open the file into your favorite spreadsheet program, and use it to identify images that are missing Alt Texts, or that need other improvement. The file also includes links to edit the post in which the images appear, so you can quickly and easily edit the attribute.

== Frequently Asked Questions ==

= What's this for? =

It's very important for accessibility to have correct Alternative Text attributes set for all of your images. This CSV file will make it easy to find images that need to be fixed, so you can improve the accessibility on your site.

Because the Alt Text is "hardcoded" into your posts and pages when you insert the image, it can be difficult to know which images don't have Alt Text, or need better ones. (If you go back and add an Alt Text to an image in the media library, it won't update your post content -- so you need to look at the actual post itself.)

= What should I put in the Alt Text field? =

[This image tutorial from w3.org is a great resource](https://www.w3.org/WAI/tutorials/images/)

= How do I edit the Alt Text on the images? =

Open the corresponding post in the editor (use the link in the `edit link` column to open the post that includes the corresponding image). Edit the image in the post, and add in the Alt Text there. Remember, this does not add the Alt Text to the image in the media library - that needs to be done separately.

= If I edit the Alt Text in the Media Library, will it edit the Alt Text in my posts? =

Unfortunately, no. Once an image is inserted into a post, it's "hardcoded" along with its Alt Text. So you'll need to edit the actual post or page itself.

If you add an Alt Text to the image in the media library (_before_ it's inserted into the post content), then it will be inserted with that Alt Text.

**Heads up!** If you're using the Block Editor, and if you use the "Image" block to insert an image and upload it directly into the block (Selecting an "Image" Block and then clicking the "Upload" button), it  inserts the image and adds it to the media library _before_ you can add the Alt Text. So you'll end up inserting the image, adding the Alt Text in the sidebar in the editor, and later find that there's no Alt Text set on the image in the media library!  

The workaround for this is to select the image block, click "Media Library" (instead of "Upload"), and then from the Media Library modal, click the "Upload Files" tab, and upload the image there. Add the Alt Text after you upload the image, and _then_ insert it into the post.

= Does the CSV file list all of my images? =

The plugin scans all of your post and page content - and should also pick up any other custom post types. However, it will not include images that are used in your theme, or any auto-generated usage (like archive pages) - and it may miss a few others here and there. We recommend you also look around your site and check the Alt Text on images outside of posts manually, or use a third-party scanning tool to make sure nothing was missed.

= What features will you be adding in the future? =

For our first release, we wanted to make a tool that would be immediately useful. We plan on expanding it soon, to include a more user-friendly interface for finding (and fixing) Alt Text.  If you have feature ideas, please let us know!

= What's the difference between EMPTY and MISSING? =

An empty (null) alt text, which is expressed like this in the code: `alt=""`, we'll note that they're `EMPTY`. (This is generally considered the best practice for "decorative" images.) 

If the image has no `alt` attribute at all, then we'll note that it's `MISSING`.

= I already entered Alt attributes, why are all my images listed? =

The plugin attempts to find all the images that are actually used in your content, and then lists all of the images it finds, along with their corresponding Alt Text (or lack thereof) on each row in the spreadsheet. This way you can see which images have correct Alt attributes set, which are already compliant, and which may need to be improved.

For example, if you had previously entered Pinterest descriptions in the Alt Text field, with lots of hashtags (which is not how the Alt attribute should be used), you can scan through each row of the spreadsheet to identify and fix those.

= The file never downloads? =

Most sites will generate the file in about 10-20 seconds. On sites with a lot of content, it may take several minutes.

If you still don't get the download file after waiting several minutes, ask your host to increase your PHP Memory Limit. In several cases, changing from 128MB to 512MB has solved this, but the actual limit necessary will depend on your site and your hosting.

To increase the memory limit yourself, [try adding this to your `wp-config.php` file](https://wordpress.org/support/article/editing-wp-config-php/#increasing-memory-allocated-to-php):

`define( 'WP_MAX_MEMORY_LIMIT', '512M' );`

You can also try changing the `memory-limit` line in PHP.ini:

`memory_limit = 512M ;`

You can also try adding this to your .htaccess file:

`php_value memory_limit 512M`


== Screenshots ==
1. Simply click the button to download a CSV file of all your images and their alt texts.

== Changelog ==

= 0.2.0 =
* First release.
