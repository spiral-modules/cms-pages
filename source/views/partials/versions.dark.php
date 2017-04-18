<dark:use path="spiral:listing/*" prefix="listing:"/>
<dark:use bundle="keeper:bundle"/>

<?php
/**
 * @var \Spiral\Pages\Database\Page $page
 * @var \Spiral\Pages\Database\Page $entity
 * @var \Spiral\Listing\Listing     $versions
 */
?>
<div class="row">
    <div class="col s12 m8">
        <div class="card z-depth-1">
            <div class="card-content">
                <listing:form listing="<?= $versions ?>">
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
            <div class="row">
                <div class="col s12">
                    <listing:grid listing="<?= $versions ?>" as="entity" color="teal"
                                  class="striped">
                        <grid:cell sorter="time_started" label="[[Time created:]]"
                                   value="<?= $entity->time_created->format('M jS, Y H:i') ?>"/>
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
    </div>
    <div class="col s12 m4">
        <vault:card>
            <?php
            foreach ($page->conditions() as $condition => $label) {
                if (isset($pageConditions[$condition])) { ?>
                    <form:checkbox label="<?= $label ?>" name="<?= $condition ?>" checked/>
                <?php } else { ?>
                    <form:checkbox label="<?= $label ?>" name="<?= $condition ?>"/>
                <?php }
            } ?>
        </vault:card>
    </div>
</div>