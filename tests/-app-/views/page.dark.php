<dark:use path="pages:cms/*" prefix="pages:"/>

<?php
/**
 * @var \Spiral\Pages\Database\Page $page
 */
?>
<!DOCTYPE html>
<html>
<head>
    <pages:meta page="<?= $page ?>" description="default description" keywords="default,keywords">
        <meta name="baz" content="default tags">
    </pages:meta>
</head>
<body>
<pages:page page="<?= $page ?>"/>
</body>
</html>