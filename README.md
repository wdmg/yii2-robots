[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.35-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Downloads](https://img.shields.io/packagist/dt/wdmg/yii2-robots.svg)](https://packagist.org/packages/wdmg/yii2-robots)
[![Packagist Version](https://img.shields.io/packagist/v/wdmg/yii2-robots.svg)](https://packagist.org/packages/wdmg/yii2-robots)
![Progress](https://img.shields.io/badge/progress-ready_to_use-green.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-robots.svg)](https://github.com/wdmg/yii2-robots/blob/master/LICENSE)

# Yii2 Robots.txt
Automatically generating and edit the robots.txt file

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.35 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [Yii2 SelectInput](https://github.com/wdmg/yii2-selectinput) widget

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-robots"`

After configure db connection, run the following command in the console:

`$ php yii pages/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations
  3) Re-generate `robots.txt` file

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-robots/migrations`

# Configure
To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'robots' => [
            'class' => 'wdmg\pages\Module',
            'routePrefix' => 'admin',
            'robotsWebRoot'  => '@webroot/robots.txt'
        ],
        ...
    ],

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('robots')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>

# Status and version [ready to use]
* v.1.0.0 - Added console operations and activity log
* v.0.0.1 - Added base migrations, bootstrap, base module and models