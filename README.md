Create an RSS feed for your Readeck installation.

# Instructions

## 1. Get your API Token

Create an API token at `https://your-readeck.com/profile/tokens`.

## 2. Update the variables in `config.php`.

Rename `config.sample.php` to `config.php` and enter the appropriate values.

## 3. Customize the `index.php` file (optional)

By default, the `index.php` will look for a `$_GET['type']` value in the URL. It's setup to recognize either "video" or "article" by default. If you don't provide a value, it will list all unread items.

There are additional parameters you can add, if you want to filter by collections, labels, etc. See the optional parameters at `https://your-readeck.com/docs/api#get-/bookmarks`.

## 4. View your feed

Visit `your-domain.com/ReadeckRSS` (or wherever your cloned this repository) to test your feed. You can also update the `$feed_url` value in to this URL in your `config.php` file in order to include the URL in the feed itself.