<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DUPX_REST_AUTH implements Requests_Auth
{
    protected $nonce = null;

    public function __construct($nonce)
    {
        $this->nonce = $nonce;
    }

    public function register(Requests_Hooks &$hooks)
    {
        $hooks->register('requests.before_request', array($this, 'before_request'));
    }

    public function before_request(&$url, &$headers, &$data, &$type, &$options)
    {
        $data['_wpnonce'] = $this->nonce;
        foreach ($_COOKIE as $key => $val) {
            $options['cookies'][$key] = $val;
        }
    }
}
