<?php
/**
 * @var \Spiral\Pages\Database\Page $page
 */
?>
<extends:layouts.basic title="[[<?= $page->title ?>]]" keywords="<?= $page->keywords ?>"
                       description="<?= $page->description ?>"/>

<block:content>
    <?= $page->source ?>
</block:content>
