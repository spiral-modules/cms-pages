<?php
/**
 * @var array                                              $statuses
 * @var \Spiral\Pages\Database\Page                        $page
 * @var \Spiral\Pages\Database\Entities\AbstractPageEntity $source
 */
?>
<extends:vault:layout title="<?= !empty($isCopy) ? '[[Copy page]]' : '[[Create page]]' ?>"
                      class="wide-content"/>

<define:actions>
    <?php if (empty($isCopy)) { ?>
        <vault:uri target="pages" class="btn-flat teal-text waves-effect" post-icon="trending_flat">
            [[Cancel]]
        </vault:uri>
    <?php } else {
        if ($source instanceof \Spiral\Pages\Database\Revision) {
            ?>
            <vault:uri target="pages:viewRevision" options="<?= ['id' => $source->primaryKey()] ?>"
                       class="btn-flat teal-text waves-effect" post-icon="trending_flat">
                [[Back]]
            </vault:uri>
        <?php } else { ?>
            <vault:uri target="pages:edit" options="<?= ['id' => $source->primaryKey()] ?>"
                       class="btn-flat teal-text waves-effect" post-icon="trending_flat">
                [[Back]]
            </vault:uri>
        <?php }
    } ?>
</define:actions>

<define:content>
    <div class="row">
        <div class="col s12 m8">
            <vault:form action="<?= vault()->uri('pages:create') ?>">
                <div class="row">
                    <div class="col s12 m6">
                        <form:input label="[[Title:]]" name="title" value="<?= $page->title ?>"/>
                        <form:input label="[[Slug:]]" name="slug" value="<?= $page->slug ?>"/>
                        <form:select label="[[Status:]]" name="status" values="<?= $statuses ?>"
                                     value="<?= $page->status ?>"/>
                        <form:textarea label="[[Meta keywords:]]" name="keywords"
                                       value="<?= $page->keywords ?>"/>
                        <form:textarea label="[[Meta description:]]" name="description"
                                       value="<?= $page->description ?>"/>
                    </div>
                    <div class="col s12 m6">
                        <form:textarea label="[[Page source:]]" name="source" rows="40"
                                       value="<?= $page->source ?>"/>
                    </div>
                </div>
                <vault:allowed permission="vault.pages.add">
                    <div class="right-align">
                        <input type="submit"
                               value="<?= !empty($isCopy) ? '[[CREATE COPY]]' : '[[CREATE]]' ?>"
                               class="btn teal waves-effect waves-light"/>
                    </div>
                </vault:allowed>
            </vault:form>
        </div>
    </div>
</define:content>