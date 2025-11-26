# CleanMod WordPress Plugin

WordPress plugin that integrates CleanMod AI comment moderation to automatically detect and handle toxic comments.

## Features

- **Automatic Moderation**: New comments are automatically sent to CleanMod's moderation API
- **Flexible Behavior**: Configure how flagged and blocked comments are handled
- **Fail-Safe**: Comments still work even if the API is unavailable
- **Native WordPress Integration**: Works with WordPress's built-in comment system

## Installation

1. Upload the `wp-cleanmod` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `Settings → CleanMod` to configure your API key

## Configuration

### API Key

Get your API key from the [CleanMod dashboard](https://cleanmod.dev/dashboard/api-keys).

1. Navigate to `Settings → CleanMod` in WordPress admin
2. Enter your CleanMod API key
3. Enable the plugin
4. Configure moderation behavior

### Moderation Behavior

**When decision is "flag":**

- **No change**: Comment passes through normally (respects WordPress default settings)
- **Hold for moderation**: Comment is held pending manual review

**When decision is "block":**

- **Hold for moderation**: Comment is held pending manual review
- **Mark as spam**: Comment is automatically marked as spam

## How It Works

1. When a new comment is submitted, the plugin intercepts it using WordPress's `pre_comment_approved` filter
2. The comment text is sent to CleanMod's `/api/v1/moderate` endpoint
3. Based on the `decision` returned (`allow`, `flag`, or `block`), the comment status is adjusted according to your settings
4. If the API is unavailable, comments proceed normally (fail-open policy)

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Valid CleanMod API key

## Support

For issues, questions, or feature requests, visit [CleanMod](https://cleanmod.dev).

## License

GPL v2 or later
