<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\content\models\Content;
use humhub\modules\content\models\forms\ScheduleOptionsForm;
use humhub\modules\ui\form\widgets\DatePicker;
use humhub\modules\ui\form\widgets\TimePicker;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var ScheduleOptionsForm $scheduleOptions */
/* @var bool $disableInputs */
?>

<?php $form = ActiveForm::begin() ?>
<?= Html::hiddenInput('state', Content::STATE_SCHEDULED) ?>
<?= Html::hiddenInput('stateTitle', $scheduleOptions->getStateTitle()) ?>
<?= Html::hiddenInput('buttonTitle', Yii::t('ContentModule.base', 'Save scheduling')) ?>
<?= Html::hiddenInput('scheduledDate', $scheduleOptions->date) ?>


<?php Modal::beginDialog([
    'title' => Yii::t('ContentModule.base', '<strong>Scheduling</strong> Options'),
    'footer' => ModalButton::cancel() . ' ' . ModalButton::submitModal(),
]) ?>

<?= $form->field($scheduleOptions, 'enabled')->checkbox() ?>
<div class="row">
    <div class="col-sm-3 col-6">
        <?= $form->field($scheduleOptions, 'date')
            ->widget(DatePicker::class, ['options' => ['disabled' => $disableInputs]])
            ->label(false) ?>
    </div>
    <div class="col-sm-3 col-6" style="padding-left:0">
        <?= $form->field($scheduleOptions, 'time')
            ->widget(TimePicker::class, ['disabled' => $disableInputs])
            ->label(false) ?>
    </div>
    <div class="col-12">
        <p class="help-block"><?= Yii::t('ContentModule.base', 'Note: Due to technical reasons there may be a delay of a few minutes.') ?></p>
    </div>
</div>

<?php Modal::endDialog() ?>

<?php ActiveForm::end() ?>

<script <?= Html::nonce() ?>>
    $('#scheduleoptionsform-enabled').click(function () {
        $(this).closest('form').find('input[type=text]').prop('disabled', !$(this).is(':checked'));
    });
</script>
