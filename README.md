# Contao CSS class replacer extension

Using this extension you can easily replace/modify/extend CSS classes of any elements on your page within one theme!
The elements you want to modify are either selected using an XPath expression or a normal CSS selector.

After the installation you can define CSS replacement rules per theme.

## Explanation

Let's say you want to give your website some Bootstrap magic and thus need to add the CSS class `row` to every article.
Instead of modifying the `mod_article` templates you can simply define a new replacement rule:

| Selector (CSS) | Replacement  |
| ------------- |:-------------:|
|  div.mod_article    |  mod_article row |

This would e.g. turn

```
<div class="mod_article first last block">
```

into this:


```
<div class="mod_article row">
```

## Simple Tokens

Since you might want to keep the existing classes instead of replacing them completely, you can just use the Simple Token `##all##` for the existing ones:

| Selector (CSS) | Replacement  |
| ------------- |:-------------:|
|  div.mod_article    |  ##all## row |

This would turn

```
<div class="mod_article first last block">
```

into this:


```
<div class="mod_article first last block row">
```

You can even specify a specific index. For example if you know that your desired class is alway on the second position, you can refer to it using `##class_2##`. Example:

| Selector (CSS) | Replacement  |
| ------------- |:-------------:|
|  div.mod_article    |  ##class_2## row |

This would turn

```
<div class="mod_article first last block">
```

into this (the second element is "first" plus "row" is added):


```
<div class="first row">
```

## Page specific replacements

Replacements are applied within the whole theme but sometimes you might want to replace elements only on a specific page, so how can we do this? Well, it's as easy as that: Use your CSS knowledge!

Give your page a CSS class `very_special` which Contao then adds to the `<body>` tag and you're good to limit your CSS replacement rules to `body.very_special`.



## Performance

Working on the whole HTML document before sending it to the browser is extensive work! Obviously the more rules you define the longer it takes! For every request sent to Contao, the extension will take its time to work on the output and apply your rules. You should thus use the page caching!
If you activate the debug mode you can see the time the replacer needs in the debug bar.