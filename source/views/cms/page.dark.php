<?php #compile
/**
 * @var \Spiral\Pages\Database\Page      $page
 * @var \Spiral\Views\DynamicEnvironment $environment
 */
$this->runtimeVariable('page', '${page}');
?>

<?php if ($page->status->isDraft()) { ?>
    <p>[[This page is in a draft mode and can be viewed only by admin users.]]</p>
<?php } ?>

<?php if ($environment->getValue('page.editable')) { #compile ?>
    <div data-piece="${page-type|html}" data-id="<?= $page->primaryKey() ?>" node:attributes>
        <?= $page->source ?>
    </div>
<?php } else { #compile ?>
    <div node:attributes>
        <?= $page->source ?>
    </div>
<?php } #compile ?>