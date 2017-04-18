<?php #compile
/**
 * @var \Spiral\Pages\Database\Page     $page
 * @var \Spiral\Listing\Listing         $revisions
 * @var \Spiral\Pages\Database\Revision $entity
 */
?>
<div class="card z-depth-1">
    <div class="card-content">
        <listing:form listing="<?= $revisions ?>">
            <div class="row">
                <div class="col s6">
                    <listing:filter>
                        <form:input name="search"
                                    placeholder="[[Find by title, slug, description, keywords or source...]]"/>
                    </listing:filter>
                </div>
                <div class="col s2">
                    <div class="right-align">
                        <listing:reset/>
                    </div>
                </div>
            </div>
        </listing:form>
    </div>
    <?php
    /**
     * @var \Spiral\Pages\Services\UniquePageContents $contents
     */
    $contents = spiral(\Spiral\Pages\Services\UniquePageContents::class);
    ?>
    <div class="row">
        <div class="col s12">
            <listing:grid listing="<?= $revisions ?>" as="entity" color="teal" class="striped">
                <grid:cell label="[[ID:]]" value="<?= $entity->primaryKey() ?>"/>
                <grid:cell sorter="time_started" label="[[Active since:]]"
                           value="<?= $entity->time_started->format('M jS, Y H:i') ?>"/>
                <grid:cell sorter="time_ended" label="[[Active till:]]"
                           value="<?= $entity->time_ended->format('M jS, Y H:i') ?>"/>
                <grid:cell label="[[Revisions content diff:]]">
                    <?php
                    $diff = $entity->diff;
                    if (empty($diff)) { ?>
                        <i>Identical</i>
                    <? } else {
                        echo e($diff);
                    } ?></grid:cell>
                <grid:cell label="[[Current content diff:]]">
                    <?php
                    $diff = $contents->calcDiff($page, $entity);
                    if (empty($diff)) { ?>
                        <i>Identical</i>
                    <? } else {
                        echo e($diff);
                    } ?>
                </grid:cell>
                <grid:cell label="[[Editor:]]">
                    <?php
                    if ($entity->editor && $entity->editor->getName()) {
                        echo e($entity->editor->getName());
                    } else {
                        echo '&mdash;';
                    }
                    ?>
                </grid:cell>
                <grid:cell class="right-align">
                    <vault:allowed permission="vault.pages.viewRevision">
                        <vault:uri target="pages:viewRevision" icon="edit"
                                   options="<?= ['id' => $entity->primaryKey()] ?>"
                                   class="btn-flat waves-effect"/>
                    </vault:allowed>
                </grid:cell>
            </listing:grid>
        </div>
    </div>
</div>
