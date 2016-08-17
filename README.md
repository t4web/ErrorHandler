# ErrorHandler
ZF2 Module. For handle and log errors

If enabled you can log all PHP errors\notices\warnings etc.

## Installation

Add this project in your composer.json:

```json
"require": {
    "t4web/error-handler": "~1.0.0"
}
```

Now tell composer to download `T4web\ErrorHandler` by running the command:

```bash
$ php composer.phar update
```

#### Post installation

Enabling it in your `application.config.php`file.

```php
<?php
return array(
    'modules' => array(
        // ...
        'T4web\Log',
        'T4web\ErrorHandler',
    ),
    // ...
);
```

## Introduction

For example, we generate some errors. In our `Application\Controller\IndexController`:
```php
public function indexAction()
{
    $e = $a['i'];  // this is Notice
    asd();  // this is Fatal
    return new ViewModel();
}
```

If you use `t4web\admin` in your logs you can see:
![backend error handler log entries](http://teamforweb.com/var/admin-error-handler.jpg)