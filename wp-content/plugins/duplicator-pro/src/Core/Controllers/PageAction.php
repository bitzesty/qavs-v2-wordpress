<?php

/**
 * Action page class
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Core\Controllers;

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Libs\Snap\SnapUtil;

class PageAction
{
    /**
     *
     * @var string
     */
    protected $key;

    /**
     *
     * @var callable
     */
    protected $callback;

    /**
     *
     * @var [string]
     */
    protected $menuSlugs = array();

    /**
     *
     * @param string $key
     * @param callable $callback
     * @param [string] $menuSlugs
     * @throws \Exception
     */
    public function __construct($key, $callback, $menuSlugs = array())
    {
        if (strlen($key) == 0) {
            throw new \Exception('action key can\'t be empty');
        }

        if (!is_callable($callback)) {
            throw new \Exception('action callback have to be callable function');
        }

        $this->key       = $key;
        $this->callback  = $callback;
        $this->menuSlugs = $menuSlugs;
    }

    /**
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     *
     * @return string
     */
    public function getNonceKey()
    {
        $result = 'dup_nonce_';
        foreach ($this->menuSlugs as $slug) {
            $result .= $slug . '_';
        }

        return str_replace(array('-', '.', '\\', '/'), '_', $result . $this->key);
    }

    /**
     *
     * @return string The token.
     */
    public function getNonce()
    {
        return wp_create_nonce($this->getNonceKey());
    }

    /**
     *
     * @param bool $echo
     * @return string
     */
    public function getActionNonceFileds($echo = true)
    {
        ob_start();
        wp_nonce_field($this->getNonceKey());
        echo '<input type="hidden" name="' . ControllersManager::QUERY_STRING_MENU_KEY_ACTION . '" value="' . $this->key . '" >';
        if ($echo) {
            ob_end_flush();
            return '';
        } else {
            return ob_get_clean();
        }
    }

    /**
     * return true if current page is the page of current action
     *
     * @param [string] $currentMenuSlugs
     * @return boolean
     */
    public function isPageOfCurrentAction($currentMenuSlugs)
    {
        foreach ($this->menuSlugs as $index => $slug) {
            if (!isset($currentMenuSlugs[$index]) || $currentMenuSlugs[$index] != $slug) {
                return false;
            }
        }

        return true;
    }

    /**
     * return true if current current action is called
     *
     * @param [string] $currentMenuSlugs
     * @param string $action
     * @return boolean
     */
    public function isCurrentAction($currentMenuSlugs, $action)
    {
        if ($action !== $this->key) {
            return false;
        }

        foreach ($this->menuSlugs as $index => $slug) {
            if (!isset($currentMenuSlugs[$index]) || $currentMenuSlugs[$index] != $slug) {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @return bool
     */
    protected function verifyNonce()
    {
        $nonce = SnapUtil::filterInputRequest(
            '_wpnonce',
            FILTER_SANITIZE_STRING,
            array(
                    'options' => array(
                        'default' => false
                    )
                )
        );
        return wp_verify_nonce($nonce, $this->getNonceKey());
    }

    /**
     *
     * @return mixed
     */
    public function exec(&$resultData = array())
    {
        $result = true;
        try {
            if (!$this->verifyNonce()) {
                throw new \Exception('Security issue on action ' . $this->key);
            }
            $funcResultData = call_user_func($this->callback);
            $resultData     = array_merge($resultData, $funcResultData);
        } catch (\Exception $e) {
            $resultData['errorMessage'] = $e->getMessage();
            $result                     = false;
        } catch (\Error $e) {
            $resultData['errorMessage'] = $e->getMessage();
            $result                     = false;
        }
        return $result;
    }
}
