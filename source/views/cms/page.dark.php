<?php #compile
/** @var \Spiral\Pages\Database\Page $page */
$this->runtimeVariable('page', '${page}');

//Show notice that current page is in draft
if ($page->status->isDraft()) {
    /** @var \Spiral\Pages\Config $config */
    $config = spiral(\Spiral\Pages\Config::class);
    if ($config->showDraftNotice()) { ?>
        <p>[[This page is in a draft mode and can be viewed only by admin users.]]</p>
    <?php }
}

/* @var \Spiral\Views\DynamicEnvironment $environment */
if ($environment->getValue('page.editable')) { ?>
    <div data-piece="${page-type|html}" data-id="<?= $page->primaryKey() ?>" node:attributes>
        <?= $page->source ?>
    </div>
<?php } else { #compile ?>
    <div node:attributes>
        <?= $page->source ?>
    </div>
<?php } #compile ?>