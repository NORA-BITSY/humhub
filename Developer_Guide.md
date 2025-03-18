# HumHub Developer Guide

## Introduction

Welcome to the HumHub Developer Guide! This guide provides comprehensive information for developers who want to contribute to HumHub, create custom modules, or integrate HumHub with other applications.

HumHub is a free social network software written in PHP with Yii Framework. It provides an easy-to-use platform for creating and managing your own social network.

## Table of Contents

1.  [Getting Started](#getting-started)
    *   [Prerequisites](#prerequisites)
    *   [Installation](#installation)
    *   [Development Environment Setup](#development-environment-setup)
2.  [Core Concepts](#core-concepts)
    *   [Architecture Overview](#architecture-overview)
    *   [Modules](#modules)
    *   [Events](#events)
    *   [Settings](#settings)
    *   [Database](#database)
    *   [Assets](#assets)
    *   [Themes](#themes)
    *   [Yii Framework](#yii-framework)
3.  [Module Development](#module-development)
    *   [Creating a New Module](#creating-a-new-module)
    *   [Module Structure](#module-structure)
    *   [Configuration](#configuration)
    *   [Controllers and Actions](#controllers-and-actions)
    *   [Views](#views)
    *   [Models](#models)
    *   [Forms](#forms)
    *   [Permissions](#permissions)
    *   [Internationalization (i18n)](#internationalization-i18n)
4.  [API Reference](#api-reference)
    *   [REST API](#rest-api)
    *   [PHP API](#php-api)
5.  [Contributing](#contributing)
    *   [Coding Standards](#coding-standards)
    *   [Pull Request Guidelines](#pull-request-guidelines)
    *   [Reporting Issues](#reporting-issues)
6.  [Troubleshooting](#troubleshooting)
    *   [Common Issues](#common-issues)
    *   [Debugging](#debugging)
    *   [FAQ](#faq)

## Getting Started

### Prerequisites

Before you start developing with HumHub, make sure you have the following installed:

*   **PHP:** Version 7.4 or higher.
*   **MySQL:** Version 5.6 or higher.
*   **Composer:**  Dependency Manager for PHP.  [https://getcomposer.org/](https://getcomposer.org/)
*   **Node.js and npm:** Required for asset management. [https://nodejs.org/](https://nodejs.org/)
*   **Git:** Version control system. [https://git-scm.com/](https://git-scm.com/)

### Installation

1.  **Download HumHub:** Get the latest version from the [HumHub website](https://www.humhub.com/en/download) or clone the repository from [GitHub](https://github.com/humhub/humhub).

    ```bash
    git clone https://github.com/humhub/humhub.git
    cd humhub
    ```

2.  **Install Dependencies:** Use Composer to install the required PHP packages.

    ```bash
    composer install
    ```

3.  **Configure Database:** Create a new MySQL database for HumHub and update the database settings in `protected/config/common.php`.

    ```php
    // filepath: protected/config/common.php
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=humhub',
        'username' => 'your_db_user',
        'password' => 'your_db_password',
        'charset' => 'utf8',
    ],
    ```

4.  **Run the Installer:** Access the HumHub installation wizard through your web browser. Follow the on-screen instructions to complete the installation.

    ```
    http://your-humhub-domain.com/install
    ```

### Development Environment Setup

1.  **Code Editor:** Choose a code editor or IDE that supports PHP development (e.g., VSCode, PhpStorm).
2.  **Debugging:** Configure Xdebug for debugging PHP code.
3.  **Asset Management:** Use npm to manage JavaScript and CSS assets.

    ```bash
    npm install
    ```

## Core Concepts

### Architecture Overview

HumHub follows a modular architecture based on the Yii Framework. Key components include:

*   **Modules:**  Self-contained units of functionality that can be enabled or disabled.
*   **Events:**  A mechanism for triggering actions and extending functionality.
*   **Widgets:** Reusable UI components.
*   **Themes:** Customizable visual styles.

### Modules

Modules are the primary way to extend HumHub's functionality. They can add new features, modify existing ones, or integrate with external services.  See the `/protected/modules` directory.

### Events

HumHub uses events to allow modules to react to specific actions or changes in the system. Modules can attach event handlers to these events to execute custom code.

```php
// filepath: mymodule/Events.php
namespace mymodule;

use humhub\modules\content\models\Content;
use yii\base\Event;

class Events
{
    public static function onContentCreated(Event $event)
    {
        $content = $event->sender;
        if ($content instanceof Content) {
            // Do something when content is created
        }
    }
}
```

To register the event, add the following to your module's `config.php`:

```php
// filepath: mymodule/config.php
return [
    'events' => [
        ['class' => Content::class, 'event' => Content::EVENT_AFTER_INSERT, 'callback' => ['mymodule\Events', 'onContentCreated']],
    ],
];
```

### Settings

Modules can define settings that can be configured by administrators. These settings are stored in the database and can be accessed through the `Yii::$app->settings` component.

```php
// filepath: MyModule.php
namespace humhub\modules\mymodule;

use Yii;

class MyModule extends \humhub\components\Module
{
    public function getConfigUrl()
    {
        return \yii\helpers\Url::to(['/mymodule/config/index']);
    }

    public static function getMySetting()
    {
        return Yii::$app->settings->get('mySetting', 'mymodule');
    }
}
```

### Database

HumHub uses MySQL as its database. Modules can define their own database tables and models using Yii's ActiveRecord.

```php
// filepath: models/MyModel.php
namespace humhub\modules\mymodule\models;

use yii\db\ActiveRecord;

class MyModel extends ActiveRecord
{
    public static function tableName()
    {
        return 'mymodule_my_model';
    }
}
```

### Assets

Assets are static files such as JavaScript, CSS, and images. Modules can define their own asset bundles to manage these files.

```php
// filepath: assets/MyModuleAsset.php
namespace humhub\modules\mymodule\assets;

use yii\web\AssetBundle;

class MyModuleAsset extends AssetBundle
{
    public $sourcePath = '@mymodule/resources';
    public $css = [
        'css/mymodule.css',
    ];
    public $js = [
        'js/mymodule.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
```

### Themes

Themes allow you to customize the look and feel of HumHub. You can create your own themes by modifying the CSS and templates.  See `/themes` directory.

### Yii Framework

HumHub is built on the Yii Framework.  Familiarity with Yii is essential for HumHub development.  See the [Yii Framework documentation](https://www.yiiframework.com/doc/guide/2.0/en).

## Module Development

### Creating a New Module

1.  **Create a Module Directory:** Create a new directory under `protected/modules` for your module (e.g., `protected/modules/mymodule`).
2.  **Create a Module Class:** Create a module class file (e.g., `protected/modules/mymodule/MyModule.php`).

    ```php
    // filepath: protected/modules/mymodule/MyModule.php
    namespace humhub\modules\mymodule;

    use humhub\components\Module;

    class MyModule extends Module
    {
        public function getConfigUrl()
        {
            return \yii\helpers\Url::to(['/mymodule/config/index']);
        }
    }
    ```

3.  **Create a `config.php` File:**  This file defines the module's configuration, including events, components, and other settings.

    ```php
    // filepath: protected/modules/mymodule/config.php
    return [
        'id' => 'mymodule',
        'class' => 'humhub\modules\mymodule\MyModule',
        'namespace' => 'humhub\modules\mymodule',
        'events' => [
            // ... event definitions ...
        ],
        'components' => [
            // ... component definitions ...
        ],
        'settings' => [
           // ... setting definitions ...
        ],
    ];
    ```

### Module Structure

A typical module structure includes the following directories:

*   `assets`: Contains asset bundles.
*   `config`: Contains the module configuration file (`config.php`).
*   `controllers`: Contains controller classes.
*   `models`: Contains model classes.
*   `migrations`: Contains database migration files.
*   `resources`: Contains static files (CSS, JavaScript, images).
*   `views`: Contains view files.
*   `widgets`: Contains widget classes.

### Configuration

The `config.php` file defines the module's configuration.  Key configuration options include:

*   `id`: The module ID.
*   `class`: The module class name.
*   `namespace`: The module namespace.
*   `events`: Event handler definitions.
*   `components`: Component definitions.
*   `params`: Module parameters.
    *   `settings`: Module settings.

### Controllers and Actions

Controllers handle user requests and actions.  Create controller classes in the `controllers` directory.

```php
// filepath: controllers/MyController.php
namespace humhub\modules\mymodule\controllers;

use humhub\modules\content\components\ContentContainerController;

class MyController extends ContentContainerController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
```

### Views

Views are responsible for rendering the user interface. Create view files in the `views` directory.

```php
// filepath: views/my/index.php
<h1>My Module</h1>
<p>This is my module's index page.</p>
```

### Models

Models represent data and business logic. Create model classes in the `models` directory.

```php
// filepath: models/MyModel.php
namespace humhub\modules\mymodule\models;

use yii\db\ActiveRecord;

class MyModel extends ActiveRecord
{
    public static function tableName()
    {
        return 'mymodule_my_model';
    }
}
```

### Forms

Forms are used to collect user input. Create form classes in the `models` directory.

```php
// filepath: models/forms/MyForm.php
namespace humhub\modules\mymodule\models\forms;

use yii\base\Model;

class MyForm extends Model
{
    public $myAttribute;

    public function rules()
    {
        return [
            [['myAttribute'], 'required'],
        ];
    }
}
```

### Permissions

Permissions control access to module features. Define permissions in your module's configuration and check them in your code.

```php
// filepath: config.php
return [
    'permissions' => [
        ['class' => 'humhub\modules\mymodule\permissions\MyPermission'],
    ],
];
```

```php
// filepath: permissions/MyPermission.php
namespace humhub\modules\mymodule\permissions;

use humhub\libs\BasePermission;

class MyPermission extends BasePermission
{
    public $id = 'my_permission';
    public $title = 'My Permission';
    public $description = 'Allows users to do something.';
}
```

### Internationalization (i18n)

HumHub supports internationalization.  Create message files in the `messages` directory for each language.

```php
// filepath: messages/en/base.php
return [
    'My Module' => 'My Module',
    'Hello, world!' => 'Hello, world!',
];
```

## API Reference

### REST API

HumHub provides a REST API for interacting with the platform programmatically.  See the [REST API documentation](link-to-rest-api-docs).

### PHP API

HumHub's PHP API allows you to access core functionalities and data.  Refer to the code documentation for details.

## Contributing

### Coding Standards

Follow the [PSR-2](https://www.php-fig.org/psr/psr-2/) coding standard.

### Pull Request Guidelines

1.  Create a new branch for your changes.
2.  Write clear and concise commit messages.
3.  Test your changes thoroughly.
4.  Submit a pull request to the `master` branch.

### Reporting Issues

Report issues on the [GitHub issue tracker](https://github.com/humhub/humhub/issues).

## Troubleshooting

### Common Issues

*   **Installation Problems:** Check the server requirements and database configuration. Ensure that all required PHP extensions are enabled.
*   **Module Errors:**  Review the module's code and configuration files. Check for any syntax errors or incorrect settings.
*   **Performance Issues:** Optimize database queries, use caching mechanisms, and minimize asset loading.  Consider using a CDN for static assets.
*   **Update Problems:** Review the #MIGRATE-DEV.md file for breaking changes.

### Debugging

*   Use Xdebug to step through PHP code. Configure your IDE to listen for Xdebug connections.
*   Check the HumHub log files in `protected/runtime/logs`.  Look for error messages, warnings, and stack traces.
*   Enable Yii's debug mode in `protected/config/common.php` to display detailed error information in the browser.
*   Use the Yii debug toolbar to inspect requests, database queries, and application state.
*   Use browser developer tools to debug JavaScript code and inspect network requests.
*   Utilize `var_dump()` or `print_r()` for quick debugging output, but remove these from production code.

### FAQ

*   **How do I create a new module?**  See the [Module Development](#module-development) section.
*   **How do I customize the look and feel of HumHub?**  See the [Themes](#themes) section.
*   **How do I contribute to HumHub?** See the [Contributing](#contributing) section.
