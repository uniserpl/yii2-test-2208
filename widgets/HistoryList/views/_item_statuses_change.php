<?php

use app\models\History;

/* @var $history History */
/* @var $oldValue string */
/* @var $newValue string */
/* @var $content string */
?>

    <div class="bg-success ">
        <?php echo "$history->eventText " .
            "<span class='badge badge-pill badge-warning'>" . ($oldValue ?? "<i>not set</i>") . "</span>" .
            " &#8594; " .
            "<span class='badge badge-pill badge-success'>" . ($newValue ?? "<i>not set</i>") . "</span>";
        ?>

        <span><?= \app\widgets\DateTime\DateTime::widget(['dateTime' => $history->ins_ts]) ?></span>
    </div>

<?php if (isset($history->user)) : ?>
    <div class="bg-info"><?= $history->user->username; ?></div>
<?php endif; ?>

<?php if (isset($content) && $content) : ?>
    <div class="bg-info">
        <?php echo $content ?>
    </div>
<?php endif; ?>