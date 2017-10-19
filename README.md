# page-specific-css
Page-specific-css is a library with extracts css selectors from html snippets and matches them with css rules to extract the used rules.

This effectively enables you to create critical css.

The bulk of it's code is based on, and expanded from, Thijs Verkoyen's inline css library.

### Usage
This library is used as a twig post processing module in JanDC/critical-css-processor. But you could of course make your own implementation.

To get a complete instruction in how to use this library (in conjunction with a twig wrapper) please refer to https://github.com/JanDC/page-specific-css-silex for the reference implementation or the critical css processor for a more generic implementation. 