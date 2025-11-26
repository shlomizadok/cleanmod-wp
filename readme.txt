=== CleanMod – AI Comment Moderation ===
Contributors: cleanmod
Tags: comments, moderation, spam, ai, content-moderation
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 0.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Uses CleanMod to detect toxic comments and automatically hold or block them.

== Description ==

CleanMod WordPress Plugin integrates CleanMod AI comment moderation to automatically detect and handle toxic comments on your WordPress site.

= Features =

* **Automatic Moderation**: New comments are automatically sent to CleanMod's moderation API
* **Flexible Behavior**: Configure how flagged and blocked comments are handled
* **Fail-Safe**: Comments still work even if the API is unavailable
* **Native WordPress Integration**: Works with WordPress's built-in comment system

= How It Works =

1. When a new comment is submitted, the plugin intercepts it using WordPress's `pre_comment_approved` filter
2. The comment text is sent to CleanMod's `/api/v1/moderate` endpoint
3. Based on the `decision` returned (`allow`, `flag`, or `block`), the comment status is adjusted according to your settings
4. If the API is unavailable, comments proceed normally (fail-open policy)

== Installation ==

1. Upload the `wp-cleanmod` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `Settings → CleanMod` to configure your API key

== Configuration ==

= API Key =

Get your API key from the [CleanMod dashboard](https://cleanmod.dev/dashboard/api-keys).

1. Navigate to `Settings → CleanMod` in WordPress admin
2. Enter your CleanMod API key
3. Enable the plugin
4. Configure moderation behavior

= Moderation Behavior =

**When decision is "flag":**

* **No change**: Comment passes through normally (respects WordPress default settings)
* **Hold for moderation**: Comment is held pending manual review

**When decision is "block":**

* **Hold for moderation**: Comment is held pending manual review
* **Mark as spam**: Comment is automatically marked as spam

== Requirements ==

* WordPress 5.0 or higher
* PHP 7.4 or higher
* Valid CleanMod API key

== Frequently Asked Questions ==

= Does this replace WordPress's built-in moderation? =

No. CleanMod adds an extra safety net on top of your existing moderation rules.

= What happens if the CleanMod API is unavailable? =

The plugin uses a fail-open policy. If the API is unavailable, comments will proceed normally without CleanMod moderation.

= Does this work with comment form plugins? =

This MVP version works with native WordPress comments only. Support for Contact Form 7, WPForms, and WooCommerce reviews may be added in future versions.

== Changelog ==

= 0.1.0 =
* Initial release
* Native WordPress comment moderation
* Configurable behavior for flagged and blocked comments
* Admin settings page

== Upgrade Notice ==

= 0.1.0 =
Initial release of CleanMod WordPress Plugin.

