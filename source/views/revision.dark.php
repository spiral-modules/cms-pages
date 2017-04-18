<?php
/**
 * @var \Spiral\Pages\Database\Revision $revision
 */
?>
<extends:vault:layout title="[[View revision]]" class="wide-content"/>

<define:actions>
    <vault:uri target="pages:edit" options="<?= ['id' => $revision->page->primaryKey()] ?>"
               class="btn-flat teal-text waves-effect" post-icon="trending_flat">
        [[Back]]
    </vault:uri>
</define:actions>

<define:content>
    <div class="row">
        <div class="col s12 m8">
            <vault:form action="<?= vault()->uri('pages:create') ?>">
                <div class="row">
                    <div class="col s12 m6">
                        <form:input label="[[Title:]]" name="title" disabled="disabled"
                                    value="<?= $revision->title ?>"/>
                        <form:input label="[[Slug:]]" name="slug" value="<?= $revision->slug ?>"
                                    disabled="disabled"/>
                        <form:textarea label="[[Meta keywords:]]" name="keywords"
                                       value="<?= $revision->keywords ?>" disabled="disabled"/>
                        <form:textarea label="[[Meta description:]]" name="description"
                                       value="<?= $revision->description ?>" disabled="disabled"/>
                    </div>
                    <div class="col s12 m6">
                        <form:textarea label="[[Page source:]]" name="source" rows="40"
                                       value="<?= $revision->source ?>" disabled="disabled"/>
                    </div>
                </div>
            </vault:form>
        </div>
        <div class="col s12 m4">
            <vault:block>
                <dl>
                    <dt>[[ID:]]</dt>
                    <dd><?= $revision->primaryKey() ?></dd>

                    <dt>[[Page ID:]]</dt>
                    <dd><?= $revision->page->primaryKey() ?></dd>

                    <dt>[[Active since:]]</dt>
                    <dd><?= $revision->time_started ?></dd>

                    <dt>[[Active till:]]</dt>
                    <dd><?= $revision->time_ended ?></dd>

                    <dt>[[Editor:]]</dt>
                    <dd>
                        <?php
                        if ($revision->editor && $revision->editor->getName()) {
                            echo e($revision->editor->getName());
                        } else {
                            echo '&mdash;';
                        }
                        ?>
                    </dd>
                </dl>
            </vault:block>
            <vault:card>

                <vault:allowed permission="vault.pages.applyRevision">
                    <div class="row">
                        <div class="col s12 m4">
                            <vault:uri target="pages:applyRevision" icon="done"
                                       options="<?= ['id' => $revision->primaryKey()] ?>"
                                       class="btn teal waves-effect waves-light"> [[Apply]]
                            </vault:uri>
                        </div>
                        <div class="col s12 m8">
                            <span
                                class="grey-text"> [[Page will be updated to this revision, its current data will be saved as a new revision.]]</span>
                        </div>
                    </div>
                </vault:allowed>
            </vault:card>
            <vault:card>
                <vault:allowed permission="vault.pages.add">
                    <div class="row">
                        <div class="col s12 m4">
                            <vault:uri target="pages:createFromRevision" icon="content_copy"
                                       options="<?= ['id' => $revision->primaryKey()] ?>"
                                       class="btn light-green waves-effect"> [[Copy]]
                            </vault:uri>
                        </div>
                        <div class="col s12 m8">
                            <span
                                class="grey-text"> [[Open "create" page with inserted fields to create a copy of the current page revision.]]</span>
                        </div>
                    </div>
                </vault:allowed>
            </vault:card>
        </div>
    </div>
</define:content>