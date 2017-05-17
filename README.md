# pages

[![Latest Stable Version](https://poser.pugx.org/spiral/pages/v/stable)](https://packagist.org/packages/spiral/pages)
[![Total Downloads](https://poser.pugx.org/spiral/pages/downloads)](https://packagist.org/packages/spiral/pages)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spiral-modules/pages/badges/quality-score.png)](https://scrutinizer-ci.com/g/spiral-modules/pages/)
[![Coverage Status](https://coveralls.io/repos/github/spiral-modules/pages/badge.svg)](https://coveralls.io/github/spiral-modules/pages)
[![Build Status](https://travis-ci.org/spiral-modules/pages.svg?branch=master)](https://travis-ci.org/spiral-modules/pages)

Spiral CMS pages module. Allows to create dynamic cms pages and manage them. <br/>
Pages contain all previous changes in revisions history. <br/>

## Installation
```
composer require spiral/pages
spiral register spiral/pages
```

Need to place navigation links in admin panel? Use example code below:
```
'pages' => [
    'title'    => 'Pages',
    'icon'     => 'description',
    'requires' => 'vault.pages',
    'items'    => [
        'pages' => ['title' => 'CMS Pages'],
        /*{{navigation.pages}}*/
    ]
],
```

##Usage

In pages config please define `page` value - a path to your view file where you will include spiral page tags:
```
<?php

//Config example
return [
    ...
    'page' => 'spiral-cms-page',
    ...
];

```

Example of page view is:
```
<dark:use path="pages:cms/*" prefix="pages:"/>

<?php
/** @var \Spiral\Pages\Database\Page $page */
?>
<!DOCTYPE html>
    <html>
    <head>
        <pages:meta page="<?= $page ?>"/>
    </head>
    <body>
        <pages:page page="<?= $page ?>"/>
    </body>
</html>
```

`pages:meta` tag supports default values, pass `keywords` or `description` with default values as attributes, tag context will be used as custom html:
```
<pages:meta page="<?= $page ?>" description="default description" keywords="default,keywords">
    <meta name="tags" content="default tags">
</pages:meta>
```

Only pages in active status are visible for users. <br/>
Admins can view them in draft when is is allowed, they need to have `viewDraftPermission` defined in pages config. <br/>
>In this case they will see some notice that this page currently in draft, you can disable notice by `showDraftNotice` value in pages config.

##On-page editing (waiting writeaway module to be finished)

If you have enough permissions (`editCMSPermission` value in pages config) you may use inline editor to change page content. <br/>
All you need is:

1. install `writeaway/writeaway` npm module
2. define get/set urls for meta data editor and source data editor

##todo
1. Add visual editor in admin panel