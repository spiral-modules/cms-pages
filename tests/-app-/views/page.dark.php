<dark:use path="pages:cms/*" prefix="pages:"/>

<?php
/**
 * @var \Spiral\Pages\Database\Page $page
 */
?>
<!DOCTYPE html>
<html>
<head>
    <pages:meta page="<?= $page ?>" description="hello"/>
</head>
<body>
<pages:page page="<?= $page ?>"/>
</body>
</html>