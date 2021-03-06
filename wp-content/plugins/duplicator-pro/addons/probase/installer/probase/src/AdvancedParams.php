<?php

/**
 * License class
 *
 * @category  Duplicator
 * @package   Installer
 * @author    Snapcreek <admin@snapcreek.com>
 * @copyright 2011-2021  Snapcreek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

namespace Duplicator\Installer\Addons\ProBase;

use Duplicator\Installer\Core\Params\Descriptors\DescriptorInterface;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\PrmMng;

class AdvancedParams implements DescriptorInterface
{
    /**
     *
     * @param \Duplicator\Installer\Core\Params\Items\ParamItem[] $params
     */
    public static function init(&$params)
    {
        $params[PrmMng::PARAM_GEN_WP_AUTH_KEY] = new ParamForm(
            PrmMng::PARAM_GEN_WP_AUTH_KEY,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Auth Keys:',
            'checkboxLabel' => 'Generate New Unique Authentication Keys and Salts',
            'status'        => License::getType() >= License::TYPE_FREELANCER ? ParamForm::STATUS_ENABLED : ParamForm::STATUS_DISABLED,
            'subNote'       => License::getType() >= License::TYPE_FREELANCER ? '' : 'Available only in Freelancer and above'
            )
        );
    }

    /**
     *
     * @param \Duplicator\Installer\Core\Params\Items\ParamItem[] $params
     */
    public static function updateParamsAfterOverwrite($params)
    {
    }
}
