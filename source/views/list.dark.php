<extends:vault:layout title="[[Pages]]" class="wide-content"/>
<dark:use path="spiral:listing/*" prefix="listing:"/>

<?php #compile
/**
 * @var \Spiral\Pages\Database\Page $entity
 * @var array                       $statuses
 */
?>

<define:actions>
    <vault:allowed permission="vault.pages.add">
        <vault:uri target="pages:add" icon="add" class="btn teal waves-effect waves-light">
            [[Page]]
        </vault:uri>
    </vault:allowed>
</define:actions>

<define:content>
    <?php
    $statusClass = [
        'active'  => 'light-green-text',
        'draft'   => 'grey-text',
        'deleted' => 'red-text'
    ];
    ?>
    <div class="card z-depth-1">
        <div class="card-content">
            <listing:form listing="<?= $listing ?>">
                <div class="row">
                    <div class="col s6">
                        <listing:filter>
                            <form:input name="search"
                                        placeholder="[[Find by title, slug, description, keywords or source...]]"/>
                        </listing:filter>
                    </div>
                    <div class="col s2">
                        <listing:filter>
                            <form:select name="status" values="<?= $statuses ?>"/>
                        </listing:filter>
                    </div>
                    <div class="col s2">
                        <listing:filter>
                            <form:select name="revisions" values="<?= [
                                'has_revisions'    => '[[Has revisions]]',
                                'has_no_revisions' => '[[No revisions]]'
                            ] ?>"/>
                        </listing:filter>
                    </div>
                    <?php /*
                    <div class="col s2">
                        <listing:filter>
                            <form:select name="versions" values="<?= [
                                'has_versions'    => '[[Has versions]]',
                                'has_no_versions' => '[[No versions]]'
                            ] ?>"/>
                        </listing:filter>
                    </div>
                    */?>
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
                <listing:grid listing="<?= $listing ?>" as="entity" color="teal" class="striped">
                    <grid:cell sorter="id" label="[[ID:]]" value="<?= $entity->primaryKey() ?>"/>
                    <grid:cell sorter="title" label="[[Title:]]"
                               value="<?= e(\Spiral\Support\Strings::shorter($entity->title,
                                   100)) ?>"/>
                    <grid:cell sorter="time_created" label="[[Created:]]"
                               value="<?= $entity->time_created->format('M jS, Y H:i') ?>"/>
                    <grid:cell label="[[Revisions:]]" sorter="revisions_count"
                               value="<?= $entity->revisions_count ?>"/>
                    <?php
                    /*<grid:cell label="[[Versions:]]" sorter="versions_count"
                               value="<?= $entity->versions_count ?>"/>*/
                    ?>
                    <grid:cell label="[[Status:]]" value="<?= e($entity->status) ?>"/>
                    <grid:cell class="right-align">
                        <vault:uri target="pages:edit" icon="edit"
                                   options="<?= ['id' => $entity->primaryKey()] ?>"
                                   class="btn-flat waves-effect"/>
                    </grid:cell>
                </listing:grid>
            </div>
        </div>
    </div>
</define:content>