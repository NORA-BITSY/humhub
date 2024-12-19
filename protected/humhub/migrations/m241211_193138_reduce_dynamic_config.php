<?php

use humhub\components\InstallationState;
use humhub\libs\DatabaseCredConfig;
use yii\db\Migration;

/**
 * Class m241211_193138_reduce_dynamic_config
 */
class m241211_193138_reduce_dynamic_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (Yii::$app->installationState->hasState(InstallationState::STATE_INSTALLED)) {
            DatabaseCredConfig::load();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241211_193138_reduce_dynamic_config cannot be reverted.\n";

        return false;
    }
}
