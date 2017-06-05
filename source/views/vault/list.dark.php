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
        <vault:uri target="pages:add" icon="add" class="btn  waves-effect waves-light">
            [[Page]]
        </vault:uri>
    </vault:allowed>
</define:actions>

<define:content>
    <?php
    $statusClass = [
        'active'  => 'visibility',
        'draft'   => 'visibility_off',
        'deleted' => 'delete'
    ];
    ?>
    <vault:card>
        <listing:form listing="<?= $listing ?>">
            <div class="row">
                <div class="col s6">
                    <listing:filter>
                        <form:input name="search" placeholder="[[Find by title, slug, description, keywords or source...]]"/>
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
                            null               => '[[All pages]]',
                            'has_revisions'    => '[[Has revisions]]',
                            'has_no_revisions' => '[[No revisions]]'
                        ] ?>"/>
                    </listing:filter>
                </div>
                <div class="col s2">
                    <div class="right-align">
                        <listing:reset/>
                    </div>
                </div>
            </div>
        </listing:form>
    </vault:card>
    <vault:card>
        <listing:grid listing="<?= $listing ?>" as="entity" color="" class="striped">
            <grid:cell sorter="id" label="[[ID:]]" value="<?= $entity->primaryKey() ?>"/>
            <grid:cell sorter="title" label="[[Title:]]">
                <a href="/<?= $entity->slug ?>" target="_blank"><i class="material-icons tiny"><?= $statusClass[(string)$entity->status] ?></i> <?php
                    echo e(\Spiral\Support\Strings::shorter($entity->title, 100))
                    ?></a>
            </grid:cell>
            <grid:cell sorter="time_created" label="[[Created:]]" value="<?= $entity->time_created->format('M jS, Y H:i') ?>"/>
            <grid:cell label="[[Revisions:]]" sorter="revisions_count" value="<?= $entity->revisions_count ?>"/>
            <grid:cell class="right-align">
                <vault:uri target="pages:edit" icon="edit" options="<?= ['id' => $entity->primaryKey()] ?>" class="btn-flat waves-effect"/>
            </grid:cell>
        </listing:grid>
    </vault:card>
</define:content>