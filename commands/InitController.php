<?php

namespace wdmg\robots\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\ArrayHelper;

class InitController extends Controller
{
    /**
     * @inheritdoc
     */
    public $choice = null;

    /**
     * @inheritdoc
     */
    public $defaultAction = 'index';

    public function options($actionID)
    {
        return ['choice', 'color', 'interactive', 'help'];
    }

    public function actionIndex($params = null)
    {
        $version = Yii::$app->controller->module->version;
        $welcome =
            '╔════════════════════════════════════════════════╗'. "\n" .
            '║                                                ║'. "\n" .
            '║           ROBOTS.TXT MODULE, v.'.$version.'           ║'. "\n" .
            '║          by Alexsander Vyshnyvetskyy           ║'. "\n" .
            '║         (c) 2020 W.D.M.Group, Ukraine          ║'. "\n" .
            '║                                                ║'. "\n" .
            '╚════════════════════════════════════════════════╝';
        echo $name = $this->ansiFormat($welcome . "\n\n", Console::FG_GREEN);
        echo "Select the operation you want to perform:\n";
        echo "  1) Apply all module migrations\n";
        echo "  2) Revert all module migrations\n";
        echo "  3) Re-generate `robots.txt` file\n\n";
        echo "Your choice: ";

        if (!is_null($this->choice))
            $selected = $this->choice;
        else
            $selected = trim(fgets(STDIN));

        if ($selected == "1") {
            Yii::$app->runAction('migrate/up', ['migrationPath' => '@vendor/wdmg/yii2-robots/migrations', 'interactive' => true]);
        } else if ($selected == "2") {
            Yii::$app->runAction('migrate/down', ['migrationPath' => '@vendor/wdmg/yii2-robots/migrations', 'interactive' => true]);
        }  else if ($selected == "3") {

            if ($this->module->genRobotsTxt())
                echo $this->ansiFormat("Robots.txt has been successfully regenerated!\n", Console::FG_GREEN);
            else
                echo $this->ansiFormat("An error occurred while saving `robots.txt` file.\n", Console::FG_RED);

        } else {
            echo $this->ansiFormat("Error! Your selection has not been recognized.\n\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        echo "\n";
        return ExitCode::OK;
    }
}
