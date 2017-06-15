<extends:vault:layout title="[[Edit page]]" class="wide-content"/>
<dark:use path="pages:/*" prefix="pages:"/>

<?php #compile
/**
 * @var \Spiral\Pages\Database\Page $page
 * @var array                       $statuses
 */
?>

<define:actions>
    <vault:uri target="pages" class="btn-flat  waves-effect" post-icon="trending_flat">
        [[Back]]
    </vault:uri>
</define:actions>

<define:content>
    <tab:wrapper>
        <tab:item id="caption" title="[[Caption]]" icon="info">
            <div class="row">
                <div class="col s12 m8">
                    <vault:form action="<?= vault()->uri('pages:update', ['id' => $page->primaryKey()]) ?>">
                        <div class="row">
                            <div class="col s12 m6">
                                <form:input label="[[Title:]]" name="title" value="<?= $page->title ?>"/>
                                <form:input label="[[Slug:]]" name="slug" value="<?= $page->slug ?>"/>
                                <form:select label="[[Status:]]" name="status" values="<?= $statuses ?>" value="<?= $page->status ?>"/>
                                <form:textarea label="[[Meta keywords:]]" name="keywords" value="<?= $page->keywords ?>"/>
                                <form:textarea label="[[Meta description:]]" name="description" value="<?= $page->description ?>"/>
                                <form:textarea label="[[Other custom meta tags:]]" name="metaTags" value="<?= $page->metaTags ?>"/>
                            </div>
                            <div class="col s12 m6">
                                <form:textarea label="[[Page source:]]" name="source" rows="40" value="<?= $page->source ?>"/>
                            </div>
                        </div>
                        <div class="right-align">
                            <vault:allowed permission="vault.pages.update">
                                <input type="submit" value="[[SAVE]]" class="btn  waves-effect waves-light"/>
                            </vault:allowed>
                        </div>
                    </vault:form>
                </div>
                <div class="col s12 m4">
                    <vault:block>
                        <dl>
                            <dt>[[ID:]]</dt>
                            <dd><?= $page->primaryKey() ?></dd>

                            <dt>[[Time Created:]]</dt>
                            <dd><?= $page->time_created ?></dd>

                            <dt>[[Time Updated:]]</dt>
                            <dd><?= $page->time_updated ?></dd>

                            <dt>[[Revisions:]]</dt>
                            <dd><?= (int)$page->revisions_count ?></dd>

                            <dt>[[Editor:]]</dt>
                            <dd>
                                <?php
                                if ($page->editor && $page->editor->getName()) {
                                    echo e($page->editor->getName());
                                } else {
                                    echo '&mdash;';
                                }
                                ?>
                            </dd>
                        </dl>
                    </vault:block>
                    <vault:card>
                        <vault:allowed permission="vault.pages.add">
                            <div class="row">
                                <div class="col s12 m5">
                                    <vault:uri target="pages:createFromPage" icon="content_copy" options="<?= ['id' => $page->primaryKey()] ?>"
                                               class="btn light-green waves-effect"> [[Copy]]
                                    </vault:uri>
                                </div>
                                <div class="col s12 m7">
                                    <span class="grey-text"> [[Opens "Create page" form with filled fields.]]</span>
                                </div>
                            </div>
                        </vault:allowed>
                    </vault:card>
                    <vault:card>
                        <?php if (!$page->status->isDeleted()) { ?>
                            <vault:allowed permission="vault.pages.delete">
                                <div class="row">
                                    <div class="col s12 m5">
                                        <vault:uriconfirm target="pages:delete" icon="delete" options="<?= ['id' => $page->primaryKey()] ?>"
                                                   class="btn red waves-effect"> [[Delete]]
                                        </vault:uriconfirm>
                                    </div>
                                    <div class="col s12 m7">
                                        <span class="grey-text"> [[Delete page. It can be restored after deletion, ask your webmaster.]]
                                        </span>
                                    </div>
                                </div>
                            </vault:allowed>
                        <?php } ?>
                    </vault:card>
                </div>
            </div>
        </tab:item>
        <tab:item id="revisions" icon="view_headline" title="[[Revisions]] <?= $page->revisions_count ? ('(' . $page->revisions_count . ')') : '' ?>">
            <pages:vault.partials.revisions/>
        </tab:item>
    </tab:wrapper>
</define:content>