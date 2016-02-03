# Introduction #

The `{{#imageLink}}` parser function allows you to link internal images to external URLs or internally to other wiki pages.

# Usage #

Once [installed](Installation.md), users of your wiki will be able to use imageLink on any page.   The syntax is as follows:

```
{{#imageLink:<image>|<url or page title>|<alt text>}}
```

Where:
  * `<image>` is the name of an uploaded image file, for example "some-picture.jpg" or "Screenshot.png"
  * `<url or page title>` is either an external URL of the form `protocol://path/to/resource`
    * _Note: only protocols returned by MediaWiki's `wfUrlProtocols()` method are accepted for external links._
  * `<alt text>` is optional title text to display when a user hovers over the image.

# Examples #

For example, to link an image called `mahalo-logo.png` to mahalo.com, you could use:

```
{{#imageLink:mahalo-logo.png|http://www.mahalo.com}}
```

-or-

```
{{#imageLink:mahalo-logo.png|http://www.mahalo.com|Mahalo.com}}
```

Where the latter will display "Mahalo.com" when the user hovers over the image.