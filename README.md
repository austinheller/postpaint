# Postpaint

Postpaint lets you add custom CSS to your WordPress posts, pages, and more.

It's really as simple as that: Install the plugin, go to any post (for any post type), and write your CSS. Styles will get added to the `<head>` after all other styles are applied.

Note: Only vanilla CSS allowed. Any non-CSS tags are automatically stripped.

## Tips

- Postpaint adds a class to the `<body>` to indicate the current active theme: `theme-[THEME-TEXTDOMAIN]`. When writing post CSS, you can scope your styles so that if you change themes, your styles won't clash with the new theme's appearance:

```
body.theme-twentyfifteen #page {
  /* etc, etc */
}
```
