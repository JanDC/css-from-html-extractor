# CSS from HTML extractor

Php library which determines which css is used from html snippets.
It is used in jandc/critical-css to automatically and dynamically determine critical css on a per page basis.


### Installation
``
composer require jandc/css-from-html-extractor
``
### Usage

#### With Twig
###### Register Extension
```php
use CSSFromHTMLExtractor\Twig\Extension as ExtractorExtension;

$extension = new ExtractorExtension()
$extension->addBaseRules('path/to/css');

/** @var Twig_Environment $twig */
$twig->addExtension($extension);
```
###### Mark the regions of your templates with the provided blocks
```twig
{% fold %}
<div class="my-class">
...
</div>
{% endfold %}
```

###### Retrieve the resulting css from the extension

```php
$extension = $twigEnvironment->getExtension(ExtractorExtension::class);
$extension->buildCriticalCssFromSnippets();
```


#### Handling raw HTML
```php
$cssFromHTMLExtractor = new CssFromHTMLExtractor();
$cssFromHTMLExtractor->addBaseRules('path/to/css');
$cssFromHTMLExtractor->addHtmlToStore($rawHtml);
$extractedCss = $cssFromHTMLExtractor->buildExtractedRuleSet();
```
