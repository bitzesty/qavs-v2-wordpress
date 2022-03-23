<?php

/**
 * Singlethon class that manages rest endpoints
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Core\REST;

final class RESTManager
{
    /**
     *
     * @var self
     */
    private static $instance = null;

    /**
     *
     * @var RESTPoint[]
     */
    private $restObjs = array();

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct()
    {
        add_action('rest_api_init', array($this, 'register'));
    }

    /**
     * get rest points list
     *
     * @return AbstractRESTPoint[]
     */
    private function getRestPoints()
    {
        $basicRestPoints   = array();
        $basicRestPoints[] = new \Duplicator\RESTPoints\Versions();
        $basicRestPoints[] = new \Duplicator\RESTPoints\SubsiteNew();
        $basicRestPoints[] = new \Duplicator\RESTPoints\AddAdminAtSubsite();

        return array_filter(
            apply_filters(
                'duplicator_endpoints',
                $basicRestPoints
            ),
            function ($restPoint) {
                return is_subclass_of($restPoint, '\Duplicator\Core\REST\AbstractRESTPoint');
            }
        );
    }

    /**
     * register rest points
     */
    public function register()
    {
        foreach ($this->getRestPoints() as $obj) {
            $obj->register();
        }
    }
}
