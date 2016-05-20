<?php

namespace Boxzilla;

use Boxzilla\DI\ContainerWithPropertyAccess;
use Boxzilla\Licensing\License;

/**
 * Class Boxzilla
 *
 * @package Boxzilla
 *
 * @property array $options
 * @property Plugin $plugin
 * @property Plugin[] $plugins
 * @property License $license
 * @property Bootstrapper $bootstrapper
 *
 */
class Boxzilla extends ContainerWithPropertyAccess {}