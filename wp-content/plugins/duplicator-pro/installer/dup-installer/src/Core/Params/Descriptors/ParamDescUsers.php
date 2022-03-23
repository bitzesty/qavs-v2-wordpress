<?php

/**
 * Users params descriptions
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
use Duplicator\Installer\Core\Params\Items\ParamFormUsersReset;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescUsers implements DescriptorInterface
{

    /**
     *
     * @param ParamItem|ParamForm[] &$params
     */
    public static function init(&$params)
    {

        $params[PrmMng::PARAM_KEEP_TARGET_SITE_USERS] = new ParamForm(
            PrmMng::PARAM_KEEP_TARGET_SITE_USERS,
            ParamForm::TYPE_INT,
            ParamForm::FORM_TYPE_SELECT,
            array(
            'default'          => 0,
            'sanitizeCallback' => function ($value) {
                if (\DUPX_InstallerState::isAddSiteOnMultisite()) {
                    return 0;
                }
                // disable keep users for some db actions
                switch (PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_ACTION)) {
                    case \DUPX_DBInstall::DBACTION_CREATE:
                    case \DUPX_DBInstall::DBACTION_MANUAL:
                    case \DUPX_DBInstall::DBACTION_ONLY_CONNECT:
                        return 0;
                    case \DUPX_DBInstall::DBACTION_EMPTY:
                    case \DUPX_DBInstall::DBACTION_REMOVE_ONLY_TABLES:
                    case \DUPX_DBInstall::DBACTION_RENAME:
                        return (int) $value;
                }
            },
            'validateCallback' => function ($value) {
                if ($value == 0) {
                    return true;
                }
                foreach (ParamDescUsers::getKeepUsersByParams() as $user) {
                    if ($value == $user['id']) {
                        return true;
                    }
                }
                return false;
            }
            ),
            array(
            'status' => function () {
                if (
                    \DUPX_InstallerState::getInstance()->getMode() !== \DUPX_InstallerState::MODE_OVR_INSTALL ||
                    \DUPX_MU::newSiteIsMultisite() ||
                    \DUPX_InstallerState::isRestoreBackup()
                ) {
                    return ParamForm::STATUS_SKIP;
                }

                if (
                    \DUPX_InstallerState::isAddSiteOnMultisite() ||
                    count(ParamDescUsers::getKeepUsersByParams()) === 0
                ) {
                    return ParamForm::STATUS_DISABLED;
                } else {
                    return ParamForm::STATUS_ENABLED;
                }
            },
            'label'   => 'Keep Users:',
            'options' => function ($item) {
                $result   = array();
                $result[] = new ParamOption(0, ' - DISABLED - ');
                foreach (ParamDescUsers::getKeepUsersByParams() as $userData) {
                    $result[] = new ParamOption($userData['id'], $userData['user_login']);
                }
                return $result;
            },
            'wrapperClasses' => array('revalidate-on-change'),
            'subNote'        => 'Keep users of the current site and eliminate users of the original site.<br>' .
            '<b>Assigns all pages, posts, media and custom post types to the selected user.</b>'
            )
        );

        $params[PrmMng::PARAM_CONTENT_OWNER] = new ParamForm(
            PrmMng::PARAM_CONTENT_OWNER,
            ParamForm::TYPE_INT,
            ParamForm::FORM_TYPE_SELECT,
            array(
            'default'          => 0,
            'sanitizeCallback' => function ($value) {
                return \DUPX_InstallerState::isAddSiteOnMultisite() ? $value : 0;
            },
            'validateCallback'                          => function ($value) {
                if (!\DUPX_InstallerState::isAddSiteOnMultisite()) {
                    return true;
                }
                if ($value == 0) {
                    return false;
                }
                foreach (ParamDescUsers::getContentOwnerUsres() as $user) {
                    if ($value == $user['id']) {
                        return true;
                    }
                }
                return false;
            },
            'invalidMessage' => "When importing into a multisite you must select a user from the multisite that will own " .
            "all the posts and pages of the imported site."
            ),
            array(
            'status' => function () {
                if (!\DUPX_InstallerState::isAddSiteOnMultisiteAvaiable()) {
                    return ParamForm::STATUS_SKIP;
                }

                if (count(ParamDescUsers::getContentOwnerUsres()) === 0) {
                    return ParamForm::STATUS_DISABLED;
                } else {
                    return ParamForm::STATUS_ENABLED;
                }
            },
            'label'   => 'Content Author:',
            'options' => function ($item) {
                $result = array();
                foreach (ParamDescUsers::getContentOwnerUsres() as $userData) {
                    $result[] = new ParamOption($userData['id'], $userData['user_login']);
                }
                return $result;
            },
            'wrapperClasses' => array('revalidate-on-change'),
            'subNote'        => '<b>Author of all imported pages, posts, media and custom post types will be set to this user.</b><br>' .
            'All users of impoted site will be eliminated.</b>'
            )
        );

        $params[PrmMng::PARAM_USERS_PWD_RESET] = new ParamFormUsersReset(
            PrmMng::PARAM_USERS_PWD_RESET,
            ParamFormUsersReset::TYPE_ARRAY_STRING,
            ParamFormUsersReset::FORM_TYPE_USERS_PWD_RESET,
            array(// ITEM ATTRIBUTES
            'default' => array_map(function ($value) {
                return '';
            }, \DUPX_ArchiveConfig::getInstance()->getUsersLists()),
            'sanitizeCallback' => array('\\Duplicator\\Libs\\Snap\\SnapUtil', 'sanitizeNSCharsNewlineTrim'),
            'validateCallback' => function ($value) {
                return strlen($value) == 0 || strlen($value) >= \DUPX_Constants::MIN_NEW_PASSWORD_LEN;
            },
            'invalidMessage' => 'can\'t have less than ' . \DUPX_Constants::MIN_NEW_PASSWORD_LEN . ' characters'
            ),
            array(// FORM ATTRIBUTES
            'status' => function ($paramObj) {
                if (PrmMng::getInstance()->getValue(PrmMng::PARAM_KEEP_TARGET_SITE_USERS) > 0) {
                    return ParamForm::STATUS_DISABLED;
                } else {
                    return ParamForm::STATUS_ENABLED;
                }
            },
            'label'       => 'Existing user reset password:',
            'classes'     => 'strength-pwd-check',
            'attr'        => array(
            'title'       => \DUPX_Constants::MIN_NEW_PASSWORD_LEN . ' characters minimum',
            'placeholder' => "Reset user password"
            )
            )
        );
    }

    /**
     *
     * @return array
     */
    public static function getKeepUsersByParams()
    {
        $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);

        if (!empty($overwriteData['adminUsers'])) {
            return $overwriteData['adminUsers'];
        }

        return array();
    }

    /**
     *
     * @param null|int $subsiteId if null get current select subsite overwrite
     * @return array // restur list of content owner users
     */
    public static function getContentOwnerUsres($subsiteId = null)
    {
        $result        = array();
        $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);

        if (is_null($subsiteId)) {
            $owrIdId = PrmMng::getInstance()->getValue(PrmMng::PARAM_SUBSITE_OVERWRITE_ID);
        } else {
            $owrIdId = $subsiteId;
        }

        if ($owrIdId > 0 && !empty($overwriteData['subsites'])) {
            foreach ($overwriteData['subsites'] as $subsite) {
                if ($subsite['id'] == $owrIdId) {
                    $result = $subsite['adminUsers'];
                    break;
                }
            }
        }

        if (empty($result) && !empty($overwriteData['adminUsers'])) {
            $result = $overwriteData['adminUsers'];
        }

        if (isset($overwriteData['loggedUser'])) {
            // insert the logged in user always at the beginning of the array
            foreach ($result as $key => $user) {
                if ($user['id'] == $overwriteData['loggedUser']['id']) {
                    unset($result[$key]);
                    break;
                }
            }
            array_unshift($result, $overwriteData['loggedUser']);
        }

        return $result;
    }

    /**
     *
     * @return int
     */
    public static function getKeepUserId()
    {
        $paramsManager = PrmMng::getInstance();
        if (\DUPX_InstallerState::isAddSiteOnMultisite()) {
            return $paramsManager->getValue(PrmMng::PARAM_CONTENT_OWNER);
        } else {
            return $paramsManager->getValue(PrmMng::PARAM_KEEP_TARGET_SITE_USERS);
        }
    }

    /**
     *
     * @param ParamItem|ParamForm[] $params
     */
    public static function updateParamsAfterOverwrite($params)
    {
    }
}
