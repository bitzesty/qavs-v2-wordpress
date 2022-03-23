<?php

/**
 * Controller interface
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Core\REST;

abstract class AbstractRESTPoint
{
    const REST_NAMESPACE = 'duplicator/v1';

    protected $args     = array();
    protected $override = false;

    public function __construct()
    {
        $this->args['methods']             = $this->getMethods();
        $this->args['callback']            = array($this, 'callback');
        $this->args['permission_callback'] = array($this, 'permission');
        $this->args['args']                = $this->getArgs();
    }

    /**
     * get current endpoint route
     *
     * @return string
     */
    abstract protected function getRoute();

    /**
     * rest api permission callback
     *
     * @param \WP_REST_Request $request
     * @return boolean
     */
    abstract public function permission(\WP_REST_Request $request);

    /**
     * return methods of current rest point
     *
     * @return string|array
     */
    protected function getMethods()
    {
        return 'GET';
    }

    /**
     * return arga of current rest point
     *
     * @return array
     */
    protected function getArgs()
    {
        return array();
    }

    /**
     * retur true if current rest point is enable
     *
     * @return boolean
     */
    public function isEnable()
    {
        return true;
    }

    /**
     * Registers a REST API route.
     *
     * @return bool True on success, false on error.
     */
    public function register()
    {
        if (!$this->isEnable()) {
            return true;
        }

        return register_rest_route(self::REST_NAMESPACE, $this->getRoute(), $this->args, $this->override);
    }

    /**
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function callback(\WP_REST_Request $request)
    {
        return call_user_func(array($this, 'respond'), $request);
    }

    /**
     * REST endpoint logic.
     *
     * @param WP_REST_Request $request
     * @return \WP_REST_Response
     */
    abstract protected function respond(\WP_REST_Request $request);
}
