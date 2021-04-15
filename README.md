# Ark Static Docs

## Page Rendering Reference

We use `erusev/parsedown` (version 1.7.4) for Markdown Rendering.

We use `github-markdown-css` (version 4.0.0) for HTML Style.

```html
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/4.0.0/github-markdown.min.css' integrity='sha512-Oy18vBnbSJkXTndr2n6lDMO5NN31UljR8e/ICzVPrGpSud4Gkckb8yUpqhKuUNoE+o9gAb4O/rAxxw1ojyUVzg==' crossorigin='anonymous' />
```

## Server Config Reference

If you use Apache to load the project, you need to add the .htaccess file and open the allow override option.

```apacheconf
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

For Nginx, you should use try_files.

```nginx
server {
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }
}
```