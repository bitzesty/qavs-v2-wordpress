<?php

/**
 * Main params descriptions
 *
 * @category  Duplicator
 * @package   Installer
 * @author    Snapcreek <admin@snapcreek.com>
 * @copyright 2011-2021  Snapcreek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Hooks\HooksMng;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapLog;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamsDescriptors
{
    public static function init()
    {
        HooksMng::getInstance()->addAction('after_params_overwrite', array(__CLASS__, 'updateParamsAfterOverwrite'));
    }

    /**
     *
     * @param ParamItem|ParamForm[] &$params
     */
    public static function initParams(&$params)
    {
        ParamDescUrlsPaths::init($params);
        ParamDescController::init($params);
        ParamDescGeneric::init($params);
        ParamDescConfigs::init($params);
        ParamDescEngines::init($params);
        ParamDescValidation::init($params);
        ParamDescDatabase::init($params);
        ParamDescCPanel::init($params);
        ParamDescReplace::init($params);
        ParamDescMultisite::init($params);
        ParamDescPlugins::init($params);
        ParamDescUsers::init($params);
        ParamDescNewAdmin::init($params);
        ParamDescWpConfig::init($params);
    }

    /**
     *
     * @param ParamItem|ParamForm[] $params
     */
    public static function updateParamsAfterOverwrite($params)
    {
        Log::info('UPDATE PARAMS AFTER OVERWRITE', Log::LV_DETAILED);
        ParamDescUrlsPaths::updateParamsAfterOverwrite($params);
        ParamDescController::updateParamsAfterOverwrite($params);
        ParamDescGeneric::updateParamsAfterOverwrite($params);
        ParamDescConfigs::updateParamsAfterOverwrite($params);
        ParamDescEngines::updateParamsAfterOverwrite($params);
        ParamDescValidation::updateParamsAfterOverwrite($params);
        ParamDescDatabase::updateParamsAfterOverwrite($params);
        ParamDescCPanel::updateParamsAfterOverwrite($params);
        ParamDescReplace::updateParamsAfterOverwrite($params);
        ParamDescMultisite::updateParamsAfterOverwrite($params);
        ParamDescPlugins::updateParamsAfterOverwrite($params);
        ParamDescUsers::updateParamsAfterOverwrite($params);
        ParamDescNewAdmin::updateParamsAfterOverwrite($params);
        ParamDescWpConfig::updateParamsAfterOverwrite($params);
    }

    public static function validateNotEmpty($value)
    {
        if (is_string($value)) {
            return strlen($value) > 0;
        } else {
            return !empty($value);
        }
    }

    /**
     * sanitize path
     *
     * @param string $value
     * @return string
     */
    public static function sanitizePath($value)
    {
        $result = SnapUtil::sanitizeNSCharsNewlineTrim($value);
        return SnapIO::safePathUntrailingslashit($result);
    }

    /**
     * the path can't be empty
     *
     * @param string $value
     * @return bool
     */
    public static function validatePath($value)
    {
        return strlen($value) > 1;
    }

    /**
     * sanitize URL
     *
     * @param string $value
     * @return string
     */
    public static function sanitizeUrl($value)
    {
        $result = SnapUtil::sanitizeNSCharsNewlineTrim($value);
        if (empty($value)) {
            return '';
        }
        // if scheme not set add http by default
        if (!preg_match('/^[a-zA-Z]+\:\/\//', $result)) {
            $result = 'http://' . ltrim($result, '/');
        }
        return rtrim($result, '/\\');
    }

    /**
     * the url can't be empty
     *
     * @param string $value
     * @return bool
     */
    public static function validateUrlWithScheme($value)
    {
        if (empty($value)) {
            return false;
        }
        if (($parsed = parse_url($value)) === false) {
            return false;
        }
        if (!isset($parsed['host']) || empty($parsed['host'])) {
            return false;
        }
        return true;
    }
}
