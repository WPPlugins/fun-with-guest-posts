=== Plugin Name ===
Contributors: arickmann
Donate link: http://www.wp-fun.co.uk/fun-with-guest-posts/
Tags: Guest Post
Requires at least: 2.2
Tested up to: 2.7
Stable tag: 1.1.1

Allows you to mark posts as guest posts so that they are still searchable on your blog, but do not show the full content. Instead the post shows the excerpt and contains a link to the guest post itself.

== Description ==

The plugin automates some of the guest post publicising process by:

    * Adding a guest post URL option to the bottom of the post page;
    * Swapping the content of the post for the optional excerpt ( so the full post is not displayed, but remains searchable );
    * Adding a short note after the excerpt suggest the reader visits the site to read the full content.
    * Adding an additional theme tag to allow customisation.


== Installation ==

Add to the plugin directory then turn it on.

Once activited the write and edit post screens will have an additional advanced option, beneath custom fields, called guest post options. Enter the full address that your post / article has been published at (inlcuding http://) in this field.

You should include the full content of the post in the content section. This will not be shown to visitors but it will mean that the full contents can be search against on your blog.

Finally make sure to enter some introductory text in the optional excerpt field. This will be displayed on guest posts instead of the main content, even if it is empty, with an additional link added to it to direct readers to the website where the full version has been published.

There is also an additional function that you can use to customise your theme.

if ( $guest_url = is_guest_post() ) {
    echo ‘This post has been published at ‘.$guest_url;
}

this will return the URL, or false if it is not a guest post.

