<?php

namespace wdmg\robots;

/**
 * Yii2 Robots.txt
 *
 * @category        Module
 * @version         1.0.0
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-robots
 * @copyright       Copyright (c) 2020 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use wdmg\helpers\ArrayHelper;
use wdmg\robots\models\Rules;
use Yii;
use wdmg\base\BaseModule;
use wdmg\votes\components\Votes;
use yii\base\InvalidConfigException;

/**
 * Votes module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\robots\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = "list/index";

    /**
     * @var string, the name of module
     */
    public $name = "Robots.txt";

    /**
     * @var string, the description of module
     */
    public $description = "Generating and edit the `robots.txt` file";

    /**
     * @var string the path to webroot `robots.txt` file
     */
    public $robotsWebRoot = "@webroot/robots.txt";

    /**
     * @var string the module version
     */
    private $version = "1.0.0";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 10;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);

    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($createLink = false)
    {
        $items = [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/'. $this->id],
            'icon' => 'fa fa-robot',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id])
        ];
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        if (isset(Yii::$app->params["robots.robotsWebRoot"]))
            $this->robotsWebRoot = Yii::$app->params["robots.robotsWebRoot"];

        if (!is_string($this->robotsWebRoot))
            throw new InvalidConfigException("Module property `robotsWebRoot` must be a string.");

        if (!$this->isConsole()) {
            $model = new Rules();
            \yii\base\Event::on(get_class($model), $model::EVENT_AFTER_INSERT, function ($event) use ($model) {
                $this->genRobotsTxt();
            });
            \yii\base\Event::on(get_class($model), $model::EVENT_AFTER_UPDATE, function ($event) use ($model) {
                $this->genRobotsTxt();
            });
            \yii\base\Event::on(get_class($model), $model::EVENT_AFTER_DELETE, function ($event) use ($model) {
                $this->genRobotsTxt();
            });
        }
    }

    public function genRobotsTxt() {
        $model = new Rules();
        $rules = $model::getPublished(true);
        $list = ArrayHelper::map($rules, 'rule', 'mode', 'robot');
        $list = array_reverse($list);

        $output = '';
        if (is_countable($list)) {

            list($host, $delay, $sitemap) = '';

            foreach ($list as $robot => $items) {
                $output .= "User-agent: " . strval($robot) . "\r\n";
                foreach ($items as $rule => $mode) {
                    if ($mode == $model::RULE_MODE_ALLOW)
                        $output .= "Allow: " . strval($rule) . "\r\n";
                    elseif ($mode == $model::RULE_MODE_DISALLOW)
                        $output .= "Disallow: " . strval($rule) . "\r\n";
                    elseif ($mode == $model::RULE_MODE_CLEAN)
                        $output .= "Clean-Param: " . strval($rule) . "\r\n";
                    elseif ($mode == $model::RULE_MODE_HOST)
                        $host .= "Host: " . strval($rule) . "\r\n";
                    elseif ($mode == $model::RULE_MODE_DELAY)
                        $delay .= "Crawl-delay: " . strval($rule) . "\r\n";
                    elseif ($mode == $model::RULE_MODE_SITEMAP)
                        $sitemap .= "Sitemap: " . strval($rule) . "\r\n";
                }

                $output .= "\r\n";

            }

            if (!empty($host))
                $output .= $host;

            if (!empty($delay))
                $output .= $delay;

            if (!empty($sitemap))
                $output .= $sitemap;

        }

        $path = Yii::getAlias($this->robotsWebRoot);
        if (!empty($output) && file_exists($path) && is_writable($path)) {
            $handle = fopen($path, 'w');
            if (fwrite($handle, $output)) {
                if (!$this->isConsole()) {
                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app/modules/robots', 'Robots.txt has been successfully regenerated!')
                    );
                }
                fclose($handle);
                return true;
            } else {
                if (!$this->isConsole()) {
                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t('app/modules/robots', 'An error occurred while saving `robots.txt` file.')
                    );
                }
                fclose($handle);
                return false;
            }
        } else {
            if (!$this->isConsole()) {
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app/modules/robots',
                        'Robots.txt by path `{path}` is not exists or not writable.',
                        [
                            'path' => $path
                        ]
                    )
                );
            }
        }
        return false;
    }

    public function getRobotsTxt() {
        $path = Yii::getAlias($this->robotsWebRoot);
        if (file_exists($path) && is_writable($path)) {
            $handle = fopen($path, 'r');
            $output = fread($handle, filesize($path));
            fclose($handle);
            return $output;
        }
    }
}