# WordPress Static Export Tools

Small, independently installable WordPress plugins for preparing a site to be exported as static HTML and hosted on services such as Amazon S3.

These plugins do not crawl, download, or rewrite a WordPress site. They only remove or disable selected dynamic WordPress features before a separate static-export tool captures the site.

## Included plugins

| Plugin | Purpose |
| --- | --- |
| [`static-export-cleanup`](static-export-cleanup/static-export-cleanup.php) | Removes WordPress-only discovery metadata from rendered HTML and response headers. |
| [`disable-feeds`](disable-feeds/disable-feeds.php) | Removes feed discovery links and returns a `404` response for WordPress feed requests. |

Both plugins are intentionally narrow. Install either one or both depending on the source site and export requirements.

## What remains unchanged

The cleanup plugin deliberately preserves:

- Pages, posts, categories, tags, dates, authors, and other public routes
- Canonical URLs
- The REST API on the source WordPress installation
- WordPress Heartbeat and the block editor
- Script and stylesheet version parameters
- Theme and plugin output

Preserving these features avoids changing rendered content or breaking site administration while the export is generated.

## Installation as must-use plugins

Must-use plugins are recommended for a temporary export because they activate automatically and cannot be accidentally disabled from the standard Plugins screen.

1. Back up the source site and test the process in staging.
2. Create `wp-content/mu-plugins/` if it does not already exist.
3. Copy the desired PHP files directly into that directory:

   ```text
   wp-content/mu-plugins/static-export-cleanup.php
   wp-content/mu-plugins/disable-feeds.php
   ```

4. Clear page, object, server, and CDN caches.
5. Generate the static export.
6. Validate the exported site.
7. Remove the temporary plugin files from `mu-plugins` when the export is complete.

The files can also be placed in individual directories under `wp-content/plugins/` and activated as normal plugins.

## Export checklist

Before publishing the static copy, verify:

- Navigation and internal links
- Images, fonts, stylesheets, scripts, and downloadable files
- Canonical URLs and the final production hostname
- Category, tag, author, date, pagination, and custom post type archives that should remain public
- Redirects, custom error pages, and trailing-slash behavior
- Forms, comments, authentication, search, and other features that require a replacement service or client-side implementation
- Any absolute URLs that still point to the WordPress source

Amazon S3 serves static files only. Configure redirects, headers, access controls, TLS, and caching separately in the hosting layer, commonly with CloudFront in front of the bucket.

## Development

Run PHP syntax checks from the repository root:

```sh
php -l static-export-cleanup/static-export-cleanup.php
php -l disable-feeds/disable-feeds.php
```

There is currently no automated WordPress integration test suite. Test changes against a staging copy of each site before exporting production content.

## License

Licensed under the [GNU General Public License v2.0](LICENSE).
