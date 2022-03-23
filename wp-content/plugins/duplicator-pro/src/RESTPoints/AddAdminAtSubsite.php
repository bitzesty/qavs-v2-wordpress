<?php

/**
 * REST point to get duplicator and wordpress versions
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\RESTPoints;

use Duplicator\Libs\Snap\SnapWP;

class AddAdminAtSubsite extends \Duplicator\Core\REST\AbstractRESTPoint
{

    /**
     * return REST point route string
     *
     * @return string
     */
    protected function getRoute()
    {
        return '/multisite/subsite/addadmin';
    }

    /**
     * True if REST point is avaiable
     *
     * @return boolean
     */
    public function isEnable()
    {
        return is_multisite();
    }

    /**
     * avaiable methods
     *
     * @return string[]
     */
    public function getMethods()
    {
        return array('GET', 'POST');
    }

    /**
     * REST point arguments
     *
     * @return array
     */
    protected function getArgs()
    {
        return array(
            'blogId' => array(
                'required'          => true,
                'type'              => 'integer',
                'description'       => 'subsite id to add current logged in user',
                'validate_callback' => function ($param, \WP_REST_Request $request, $key) {
                    if (!is_numeric($param)) {
                        return false;
                    }

                    $sites = SnapWP::getSites();
                    if (!is_array($sites)) {
                        return false;
                    }

                    foreach ($sites as $site) {
                        if ($site->blog_id == $param) {
                            return true;
                        }
                    }

                    return false;
                }
            ),
        );
    }

    /**
     *
     * @global string           $wp_version wordpress version value
     * @param  \WP_REST_Request $request request data
     * @return \WP_REST_Response
     */
    protected function respond(\WP_REST_Request $request)
    {
        $response = array(
            'success'     => false,
            'message'     => ''
        );

        try {
            if (!class_exists('WP_Network')) {
                throw new \Exception('the current version of wordpress does not support this action.');
            }

            $blogId = $request->get_param('blogId');
            $userId = get_current_user_id();

            if (!is_user_member_of_blog($userId, $blogId)) {
                $result = add_user_to_blog($blogId, $userId, 'administrator');
                if ($result instanceof \WP_Error) {
                    $response['success'] = false;
                    throw new \Exception($result->get_error_message());
                }
            }

            $response['success']     = true;
            return new \WP_REST_Response($response, 200);
        } catch (\Exception $e) {
            $exception = $e;
        } catch (\Error $e) {
            $exception = $e;
        }

        $response['success'] = false;
        $response['message'] = $exception->getMessage();

        return new \WP_REST_Response($response, 200);
    }

    /**
     *
     * @param  \WP_REST_Request $request request data
     * @return \WP_Error|boolean
     */
    public function permission(\WP_REST_Request $request)
    {
        if (!current_user_can('manage_options') || !is_super_admin() || !check_ajax_referer('wp_rest', false, false)) {
            return new \WP_Error('rest_forbidden', esc_html__('You cannot execute this action.'));
        }
        return true;
    }
}
