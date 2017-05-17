<?php #compile

/**
 * @var \Spiral\Views\DynamicEnvironment $environment
 * @var \Spiral\Pages\Database\Page      $page
 * @var string                           $page_metadata_description
 * @var string                           $page_metadata_keywords
 * @var string                           $page_metadata_metaTags
 */
$this->runtimeVariable('page', '${page}');
$this->runtimeVariable('page_metadata_description', '${description}');
$this->runtimeVariable('page_metadata_keywords', '${keywords}');
$this->runtimeVariable('page_metadata_metaTags', '${context}');
?>

<?php
/** @var \Spiral\Pages\Pages $pages */
$pages = spiral(\Spiral\Pages\Pages::class);
$meta = $pages->getMeta($page, [
    'description' => $page_metadata_description,
    'keywords'    => $page_metadata_keywords,
    'metaTags'    => $page_metadata_metaTags
]);

if (!empty($meta['metaTags'])) {
    echo $meta['metaTags'];
} ?>

    <title><?= e($meta['title']) ?></title>
    <meta name="keywords" content="<?= e($meta['keywords']) ?>">
    <meta name="description" content="<?= e($meta['description']) ?>">

<?php if ($environment->getValue('page.editable')) { #compile ?>
    <script>
        window.metadata = <?= json_encode($meta) ?>;
    </script>
<?php } #compile ?>