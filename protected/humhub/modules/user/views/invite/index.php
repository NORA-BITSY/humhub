<?php

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\admin\permissions\ManageGroups;
use humhub\modules\admin\permissions\ManageUsers;
use humhub\modules\user\models\forms\Invite;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;
use yii\helpers\Url;

/**
 * @var $this View
 * @var $model Invite
 * @var $canInviteByEmail bool
 * @var $canInviteByLink bool
 */
?>

<?php Modal::beginDialog([
    'title' => Yii::t('UserModule.invite', '<strong>Invite</strong> new people'),
    'footer' => ModalButton::cancel(Yii::t('base', 'Close')),
]) ?>

    <?php if ($canInviteByEmail && $canInviteByLink): ?>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab"
                   href="#invite-by-email"><?= Yii::t('UserModule.base', 'Invite by email') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab"
                   href="#invite-by-link"><?= Yii::t('UserModule.base', 'Invite by link') ?></a>
            </li>
        </ul>
    <?php endif; ?>

    <!-- Tab panes -->
    <div class="tab-content">
        <?php if ($canInviteByEmail): ?>
            <div class="tab-pane active" id="invite-by-email">
                <br/>
                <strong><?= Yii::t('UserModule.invite', 'Please add the email addresses of people you want to invite below.') ?></strong>
                <br/><br/>
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'emails')->textarea(['rows' => '3', 'placeholder' => Yii::t('UserModule.invite', 'Email address(es)'), 'id' => 'emails'])->label(false)->hint(Yii::t('UserModule.invite', 'Separate multiple email addresses by comma.')); ?>
                <a href="#" class="btn btn-primary" data-action-click="ui.modal.submit"
                   data-action-url="<?= Url::to(['/user/invite']) ?>" data-ui-loader>
                    <?= Yii::t('UserModule.invite', 'Send invite') ?>
                </a>
                <?php ActiveForm::end(); ?>
            </div>
        <?php endif; ?>

        <?php if ($canInviteByLink): ?>
            <div class="tab-pane<?= $canInviteByEmail ? '' : ' active' ?>" id="invite-by-link">
                <br/>
                <strong>
                    <?= Yii::t(
                        'SpaceModule.base',
                        'You can invite external users who are currently not registered via link. All you need to do is share this secure link with them.'
                    ) ?>
                </strong>
                <br/><br/>
                <div class="input-group" style="width: 100%;">
                    <?= Html::textarea('secureLink', $model->getInviteLink(), ['readonly' => 'readonly', 'class' => 'form-control']) ?>
                    <?php if (Yii::$app->user->can([ManageUsers::class, ManageGroups::class])): ?>
                        <a href="#" class="float-end"
                           data-action-confirm-header="<?= Yii::t('SpaceModule.base', 'Create new link') ?>" ,
                           data-action-confirm="<?= Yii::t('SpaceModule.base', 'Please note that any links you have previously created will become invalid as soon as you create a new one. Would you like to proceed?') ?>"
                           data-action-click="ui.modal.load"
                           data-action-click-url="<?= Url::to(['/user/invite/reset-invite-link', 'target' => $model->target]) ?>">
                            <small><?= Yii::t('SpaceModule.base', 'Create new link'); ?></small>
                        </a>
                    <?php endif; ?>
                </div>
                <br>
                <?= Button::primary(Yii::t('SpaceModule.base', 'Send the link via email'))
                    ->link('mailto:' .
                        '?subject=' . rawurlencode(Yii::t('UserModule.invite', 'You\'ve been invited to join %appName%', ['%appName%' => Yii::$app->name])) .
                        '&body=' . rawurlencode($this->renderFile($this->findViewFile('@humhub/modules/user/views/mails/plaintext/UserInvite'), [
                            'originator' => Yii::$app->user->identity,
                            'registrationUrl' => $model->getInviteLink()
                        ])))
                    ->id('global-invite-send-link-by-email-btn')
                    ->icon('paper-plane')
                    ->loader(false)
                ?>
            </div>
        <?php endif; ?>
    </div>

<?php Modal::endDialog() ?>

<script <?= Html::nonce() ?>>
    $(function () {
        $('textarea[name="secureLink"]').click(function () {
            $(this).select();
            navigator.clipboard.writeText($(this).val());
            const successMsg = <?= json_encode(Yii::t('UserModule.base', 'The secure link has been copied in your clipboard!'), JSON_HEX_TAG) ?>;
            humhub.modules.ui.status.success(successMsg);
        });
    });
</script>
