# Contao CSS class replacer extension

Using this extension you can easily replace/modify/extend CSS classes of any elements on your page within one theme!
The elements you want to modify are either selected using an XPath expression or a normal CSS selector.

After the installation you can define CSS replacement rules per theme.

## Add directives

Let's say you want to give your website some Bootstrap magic and thus need to add the CSS class `row` to every article.
Instead of modifying the `mod_article` templates you can simply define a new replacement rule:

| Selector (CSS) | Enable add directives  | Add directives  |
| ------------- |:-------------:|:-------------:|
|  div.mod_article    |  true | `row` |

This would e.g. turn

```
<div class="mod_article first last block">
```

into this:


```
<div class="mod_article first last row">
```

## Replace directives

Replacement directives are a little more complicated than add directives because they essentially cover 3 use cases:

* Replace an existing class using a simple string comparison
* Delete an existing class
* Replace an existing class using a regular expression


### Replace an existing class using a simple string comparison


| Selector (CSS) | Enable replacement directives  | Replacement directives  |
| ------------- |:-------------:|:-------------:|
|  div.mod_article    |  true | `first` -> `not_first` |

This would turn

```
<div class="mod_article first last block">
```

into this:


```
<div class="mod_article not_first last block">
```


### Delete an existing class


| Selector (CSS) | Enable replacement directives  | Replacement directives  |
| ------------- |:-------------:|:-------------:|
|  div.mod_article    |  true | `first` -> `(blank)` |

`(blank)` in this case does not mean you have to enter `(blank)` but rather just leave it blank.

This would turn

```
<div class="mod_article first last block">
```

into this:


```
<div class="mod_article last block">
```



### Replace an existing class using a regular expression

This is very, very powerful but obviously also a bit slower than simple string replacements.

Let's say you want to replace all `level_*` of your navigation module by `different_level_*`. You can do this with the simple string comparison but you would need to define rules for every level like this:

* level_1 -> different_level_1
* level_2 -> different_level_2
* etc.

This is tedious, so we use the regular expression feature.
Regular expression replacement directives are indicated by the prefix `r:` whereas the `r` obviously stands for `regular expression`:

| Selector (CSS) | Enable replacement directives  | Replacement directives  |
| ------------- |:-------------:|:-------------:|
|  .mod_navigation ul    |  true | `r:/level_(\d)/` -> `different_level_$1` |

This would turn

```
<nav class="mod_navigation">
	<ul class="level_1">
		<ul class="level_2">
```

into this:


```
<nav class="mod_navigation">
	<ul class="different_level_1">
		<ul class="different_level_2">
```

## Page specific replacements

Replacements are applied within the whole theme but sometimes you might want to replace elements only on a specific page, so how can we do this? Well, it's as easy as that: Use your CSS knowledge!

Give your page a CSS class `very_special` which Contao then adds to the `<body>` tag and you're good to limit your CSS replacement rules to `body.very_special`.



## Performance

Working on the whole HTML document before sending it to the browser is extensive work! Obviously the more rules you define the longer it takes! For every request sent to Contao, the extension will take its time to work on the output and apply your rules. You should thus use page caching!

If you activate the debug mode you can see the time the replacer needs in the debug bar.