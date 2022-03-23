<?php

/**
 * Multisite params descriptions
 *
 * @category  Duplicator
 * @package   Installer
 * @author    Snapcreek <admin@snapcreek.com>
 * @copyright 2011-2021  Snapcreek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Items\ParamOption;
use Duplicator\Installer\Core\Params\Items\ParamFormURLMapping;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapJson;
use Duplicator\Libs\Snap\SnapURL;
use Duplicator\Libs\Snap\SnapUtil;
use DUPX_InstallerState;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescMultisite implements DescriptorInterface
{

    /**
     *
     * @param ParamItem|ParamForm[] &$params
     */
    public static function init(&$params)
    {
        $archive_config = \DUPX_ArchiveConfig::getInstance();

        $params[PrmMng::PARAM_SUBSITE_ID] = new ParamForm(
            PrmMng::PARAM_SUBSITE_ID,
            ParamForm::TYPE_INT,
            ParamForm::FORM_TYPE_SELECT,
            array(
                'default'      => -1,
                'acceptValues' => array(__CLASS__, 'getSubSiteIdsAcceptValues')
            ),
            array(
                'status' => function (ParamItem $paramObj) {
                    if (
                        DUPX_InstallerState::isInstType(
                            array(
                                DUPX_InstallerState::INSTALL_STANDALONE,
                                DUPX_InstallerState::INSTALL_SUBSITE_ON_SUBDOMAIN,
                                DUPX_InstallerState::INSTALL_SUBSITE_ON_SUBFOLDER
                            )
                        )
                    ) {
                        return ParamForm::STATUS_ENABLED;
                    } else {
                        return ParamForm::STATUS_DISABLED;
                    }
                },
                'label'          => 'Subsite:',
                'wrapperClasses' => array('revalidate-on-change'),
                'options'        => array(__CLASS__, 'getSubSiteIdsOptions'),
            )
        );

        $params[PrmMng::PARAM_SUBSITE_OVERWRITE_ID] = new ParamForm(
            PrmMng::PARAM_SUBSITE_OVERWRITE_ID,
            ParamForm::TYPE_INT,
            ParamForm::FORM_TYPE_SELECT,
            array(
                'default'          => 0,
                'validateCallback' => array(__CLASS__, 'overwriteSubSiteIdValidation'),
                'invalidMessage'   => 'Select a valid subsite overwrite value'
            ),
            array(
                'status' => function (ParamItem $paramObj) {
                    if (!DUPX_InstallerState::isAddSiteOnMultisiteAvaiable()) {
                        return ParamForm::STATUS_SKIP;
                    }

                    return DUPX_InstallerState::isAddSiteOnMultisite() ? ParamForm::STATUS_ENABLED : ParamForm::STATUS_DISABLED;
                },
                'label'          => 'Action:',
                'wrapperClasses' => array('revalidate-on-change'),
                'options'        => array(__CLASS__, 'getOverwriteSubsiteIdsOptions')
            )
        );

        $params[PrmMng::PARAM_SUBSITE_OVERWRITE_NEW_SLUG] = new ParamForm(
            PrmMng::PARAM_SUBSITE_OVERWRITE_NEW_SLUG,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            array(
                'default'          => '',
                'sanitizeCallback' => function ($value) {
                    $result = SnapUtil::sanitizeNSCharsNewlineTrim($value);
                    return preg_replace('/[\s"\'\\\\\/&?#,\.:;]+/m', '', $result);
                },
                'validateCallback'                                             => function ($value) {
                    if (
                        !DUPX_InstallerState::isAddSiteOnMultisite() ||
                        PrmMng::getInstance()->getValue(PrmMng::PARAM_SUBSITE_OVERWRITE_ID) > 0
                    ) {
                        return true;
                    }

                    if (strlen($value) == 0) {
                        return false;
                    }

                    $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);

                    if (!isset($overwriteData['subsites'])) {
                        return true;
                    }

                    $parseUrl       = SnapURL::parseUrl($overwriteData['urls']['home']);
                    $mainSiteDomain = SnapURL::wwwRemove($parseUrl['host']);
                    $mainSitePath   = SnapIO::trailingslashit($parseUrl['path']);
                    $subdomain      = (isset($overwriteData['subdomain']) && $overwriteData['subdomain']);

                    foreach ($overwriteData['subsites'] as $subsite) {
                        $subsite['domain'] . $subsite['path'];

                        if ($subdomain) {
                            if (strcmp($value . '.' . $mainSiteDomain, $subsite['domain']) === 0) {
                                return false;
                            }
                        } else {
                            if (strcmp($mainSitePath . $value, SnapIO::untrailingslashit($subsite['path'])) === 0) {
                                return false;
                            }
                        }
                    }

                    return true;
                },
                'invalidMessage' => 'The new subsite slug can\'t be empty and cannot belong to an existing subsite'
            ),
            array(
                'status' => function (ParamForm $param) {
                    if (!DUPX_InstallerState::isAddSiteOnMultisiteAvaiable()) {
                        return ParamForm::STATUS_SKIP;
                    }

                    if (!DUPX_InstallerState::isAddSiteOnMultisite()) {
                        return ParamForm::STATUS_DISABLED;
                    }

                    if (PrmMng::getInstance()->getValue(PrmMng::PARAM_SUBSITE_OVERWRITE_ID) > 0) {
                        return ParamForm::STATUS_DISABLED;
                    }

                    return ParamForm::STATUS_ENABLED;
                },
                'label'  => 'New Subsite URL:',
                'prefix' => function (ParamForm $param) {
                    $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
                    $urlNew        = $overwriteData['urls']['home'];
                    $parseUrl      = SnapURL::parseUrl($urlNew);

                    $result = array('type' => 'label');
                    if (isset($overwriteData['subdomain']) && $overwriteData['subdomain']) {
                        $result['label'] = $parseUrl['scheme'] . '://';
                    } else {
                        $result['label']          = $urlNew . '/';
                        $result['attrs']['title'] = $result['label'];
                    }
                    return $result;
                },
                'postfix' => function (ParamForm $param) {
                    $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
                    if (!isset($overwriteData['subdomain']) || !$overwriteData['subdomain']) {
                        return array('type' => 'none');
                    }
                    $urlNew   = $overwriteData['urls']['home'];
                    $parseUrl = SnapURL::parseUrl($urlNew);

                    $result                   = array(
                        'type'  => 'label',
                        'label' => '.' . SnapURL::wwwRemove($parseUrl['host'])
                    );
                    $result['attrs']['title'] = $result['label'];
                    return $result;
                },
                'wrapperClasses' => array('revalidate-on-change')
            )
        );

        $params[PrmMng::PARAM_REPLACE_MODE] = new ParamForm(
            PrmMng::PARAM_REPLACE_MODE,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_RADIO,
            array(
                'default'      => 'legacy',
                'acceptValues' => array(
                    'legacy',
                    'mapping'
                )
            ),
            array(
                'label'   => 'Replace Mode:',
                'options' => array(
                    new ParamOption('legacy', 'Standard', ParamOption::OPT_ENABLED, array('title' => 'Set the files current date time to now')),
                    new ParamOption('mapping', 'Mapping', ParamOption::OPT_ENABLED, array('title' => 'Keep the files date time the same'))
                )
            )
        );

        $params[PrmMng::PARAM_MU_REPLACE] = new ParamFormURLMapping(
            PrmMng::PARAM_MU_REPLACE,
            ParamFormURLMapping::TYPE_ARRAY_STRING,
            ParamFormURLMapping::FORM_TYPE_URL_MAPPING,
            array(
                'default' => $archive_config->getNewUrlsArrayIdVal()
            ),
            array()
        );

        $params[PrmMng::PARAM_MULTISITE_CROSS_SEARCH] = new ParamForm(
            PrmMng::PARAM_MULTISITE_CROSS_SEARCH,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            array(
                'default' => (count($archive_config->subsites) <= MAX_SITES_TO_DEFAULT_ENABLE_CORSS_SEARCH)
            ),
            array(
                'status' => function ($paramObj) {
                    if (\DUPX_MU::newSiteIsMultisite()) {
                        return ParamForm::STATUS_ENABLED;
                    } else {
                        return ParamForm::STATUS_SKIP;
                    }
                },
                'label'         => 'Database search:',
                'checkboxLabel' => 'Cross-search between the sites of the network.'
            )
        );
    }

    /**
     *
     * @param ParamItem|ParamForm[] $params
     */
    public static function updateParamsAfterOverwrite($params)
    {
    }

    /**
     *
     * @return \ParamOption[]
     */
    public static function getSubSiteIdsOptions()
    {
        $archive_config = \DUPX_ArchiveConfig::getInstance();
        $options        = array();
        foreach ($archive_config->subsites as $subsite) {
            $optStatus = !DUPX_InstallerState::isImportFromBackendMode() || (count($subsite->filtered_tables) === 0 && count($subsite->filtered_paths) === 0) ? ParamOption::OPT_ENABLED : ParamOption::OPT_DISABLED;
            $label     = $subsite->blogname . ' [' . $subsite->domain . $subsite->path . ']';
            $options[] = new ParamOption($subsite->id, $label, $optStatus);
        }
        return $options;
    }

    /**
     *
     * @return int[]
     */
    public static function getSubSiteIdsAcceptValues()
    {
        $archive_config = \DUPX_ArchiveConfig::getInstance();
        $acceptValues   = array(-1);
        foreach ($archive_config->subsites as $subsite) {
            if (!DUPX_InstallerState::isImportFromBackendMode() || (count($subsite->filtered_tables) === 0 && count($subsite->filtered_paths) === 0)) {
                $acceptValues[] = $subsite->id;
            }
        }
        return $acceptValues;
    }

    /**
     *
     * @return \ParamOption[]
     */
    public static function getOverwriteSubsiteIdsOptions()
    {
        $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
        $options       = array();

        if (!is_array($overwriteData) || !isset($overwriteData['subsites'])) {
            return $options;
        }

        $usersOptions = ParamDescUsers::getContentOwnerUsres(0);
        $options[]    = new ParamOption(
            0,
            'Add as a new subsite',
            ParamOption::OPT_ENABLED,
            array(
                'data-keep-users' => SnapJson::jsonEncode($usersOptions)
            )
        );

        foreach ($overwriteData['subsites'] as $subsite) {
            $label        = 'Overwrite ' . $subsite['blogname'] . ' [' . $subsite['domain'] . $subsite['path'] . ']';
            $usersOptions = ParamDescUsers::getContentOwnerUsres($subsite['id']);
            $option       = new ParamOption(
                $subsite['id'],
                $label,
                ParamOption::OPT_ENABLED,
                array(
                    'data-keep-users' => SnapJson::jsonEncode($usersOptions)
                )
            );
            $option->setOptGroup('OVERWRITE');
            $options[] = $option;
        }

        return $options;
    }

    /**
     *
     * @return int[]
     */
    public static function overwriteSubSiteIdValidation($value)
    {
        if (!DUPX_InstallerState::isAddSiteOnMultisite()) {
            return true;
        }

        if ($value < 0) {
            return false;
        }

        if ($value == 0) {
            return true;
        }

        $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
        if (!is_array($overwriteData) || !isset($overwriteData['subsites'])) {
            return false;
        }

        foreach ($overwriteData['subsites'] as $subsite) {
            if ($value == $subsite['id']) {
                return true;
            }
        }

        return false;
    }
}
