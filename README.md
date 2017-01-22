# page-specific-css
Small php library in combination with a twig extention that allows you to annotate above-the-fold-html in your twig files which will than extract all the necessary CSS.

In combination with loadCSS you can setup a page-specific critcal CSS solution quite easy.

## Install
```bash
composer install page-specific-css
```

## Usage
```twig
{% fold %}
    <button class=”your-css-class”>Button</button>
{% endfold %}
```

## Usage
```twig
<!-- output -->
<head>
    <style>
        .your-css-class {
            color: pink;
        }
    </style>
</head>
```
