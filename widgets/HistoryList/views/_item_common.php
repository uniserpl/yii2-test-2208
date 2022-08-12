<?php

use yii\helpers\Html;
use app\widgets\DateTime\DateTime;

/* @var $this \yii\web\View */
/* @var $history \app\models\History */
/* @var $user \app\models\User */
/* @var $afterBody string */
/* @var $footer string */
/* @var $footerDatetime string */
/* @var $bodyDatetime string */
/* @var $iconClass string */

// Вместо передачи отдельно в каждом случае: user, ins_ts, а также body,
//     лучше передать в шаблон сразу $history и извлечь их на месте
$user = $history->user;
$footerDatetime = $history->ins_ts;

?>
<?php echo Html::tag('i', '', ['class' => "icon icon-circle icon-main white $iconClass"]); ?>

    <div class="bg-success ">
        <?php 
            echo $this->render('//obj/bodies/' . $history->getObjName('unknown'), ['history' => $history]);
            echo isset($afterBody) ? $afterBody : '';
        ?>

        <?php if (isset($bodyDatetime)) : ?>
            <span>
                <?= DateTime::widget(['dateTime' => $bodyDatetime]) ?>
            </span>
        <?php endif; ?>
    </div>

<?php if (isset($user)) : ?>
    <div class="bg-info"><?= $user->username; ?></div>
<?php endif; ?>

<?php if (isset($content) && $content) : ?>
    <div class="bg-info">
        <?php echo $content ?>
    </div>
<?php endif; ?>

<?php if (isset($footer) || isset($footerDatetime)) : ?>
    <div class="bg-warning">
        <?php echo isset($footer) ? $footer : '' ?>
        <?php if (isset($footerDatetime)) : ?>
            <span><?= DateTime::widget(['dateTime' => $footerDatetime]) ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>