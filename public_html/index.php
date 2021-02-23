<?php


define('ACCESS__', true);

header('Content-Type:text/html;charset=utf-8');
session_start();

require_once 'config.php';
require_once 'core/base/settings/internalSettings.php';

use core\base\controller\BaseRoute;
use core\base\exceptions\DbException;
use core\base\exceptions\GameException;
use core\base\exceptions\RouteException;

try {
    BaseRoute::routeDirection();
} catch (RouteException $e) {
    exit($e->getMessage());
} catch (DbException $e) {
    exit($e->getMessage());
} catch (GameException $e) {
    exit($e->getMessage());
}

