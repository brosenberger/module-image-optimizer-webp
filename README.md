# Image Optimizer WEBP Converter - a Magento 2 converter module for WEBP images

This module provides a WEBP image converter for Magento 2. It is based on the [brocode/module-image-optimizer](https://github.com/brosenberger/module-image-optimizer)

[!["Buy Me A Coffee"](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/brosenberger)

## Installation

```
composer require brocode/module-image-optimizer-webp
bin/magento module:enable BroCode_ImageWebpOptimizer
bin/magento setup:upgrade
```

## Configuration

The configuration can be found under `Stores -> Configuration -> Services -> BroCode ImageOptimizer -> Image Webp`. Currently the image quality can be set (value between 0 and 100) and the converter can be disabled.

### Apache Configuration

Add following snippet to the .htaccess file, which serves public images that are converted:

```
 ############################################
 ## if client accepts webp, rewrite image urls to use webp version
AddType image/webp .webp
RewriteCond %{HTTP_ACCEPT} image/webp
RewriteCond %{REQUEST_FILENAME} (.*)\.(png|gif|jpe?g)$
RewriteCond %{REQUEST_FILENAME}\.webp -f
RewriteRule ^ %{REQUEST_FILENAME}\.webp [L,T=image/webp]
```

### Nginx Configuration

Merge into the project nginx vhost (`nginx.conf.sample`). Place the `map` in `http {}`; add the `location` before Magento's generic static `location` under `/media/`.

```
# In http { } (once per nginx instance or included vhost file)
map $http_accept $webp_suffix {
    default "";
    "~*webp" ".webp";
}

# In server { }
location ~* ^/media/.+\.(png|gif|jpe?g)$ {
    add_header Vary Accept;
    try_files $uri$webp_suffix $uri $uri/ /get.php$is_args$args;
}
```

## Further Information

See base module for more informations on how to setup the image optimizer: [brocode/module-image-optimizer](https://github.com/brosenberger/module-image-optimizer)