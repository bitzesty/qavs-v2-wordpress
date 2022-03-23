<?php

/**
 * Installer params manager
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Params\Items\ParamForm;

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

interface DescriptorInterface
{

    /**
     *
     * @param  ParamItem|ParamForm[] &$params
     */
    public static function init(&$params);

    /**
     *
     * @param ParamItem|ParamForm[] $params
     */
    public static function updateParamsAfterOverwrite($params);
}
