<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\History;
use app\models\obj\Task;

/**
 * Команда для проверки рефакторинга
 * (быстрая замена тестам)
 *
 * @author Uniser <uniserpl@gmail.com>
 * @since 2022.08.11
 */
class TestController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }
    
    public function actionTask() {
        $task = Task::findOne('338946');
        var_export($task->attributes);
    }
    
    public function actionEventsLabel() {
        var_export(History::getEventTextByEvent('completed_task'));
        var_export(History::getEventTexts());
    }
    
}
