<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Site header and Proxy Setting
 *
 * @author brian.wang
 */
class Site_header_th_lib
{
    private $headers = array(
        'proxy' => '',
        'headers' => array (
            "VIEW_COUNTRY"              => "th",
            "VIEW_LANGUAGE"             => "th-th",
            "VIEW_CURRENCY"             => "฿",
            "VIEW_COUNTRY_REGION"       => "th",
            "VIEW_BC_CODE"              => "D",
            "VIEW_TIMEZONE"             => "Asia/Bangkok",
            "CI_VIEW_SOURCE_DOMAIN"     => "dev1.ux",
            "CI_API_SOURCE_DOMAIN"      => "dev1.ux",
            "CI_INSIDE_SOURCE_DOMAIN"   => "dev1.ux",
            "CI_FIXED_SOURCE_DOMAIN"    => "dev1.ux"
        )
    );

    public function local()
    {
        return $this->headers;
    }

    public function dev()
    {
        $this->headers['proxy'] = '10.1.24.98:8080';

        return $this->headers;
    }

    public function beta()
    {
        $this->headers['proxy'] = '10.1.24.98:8080';
        $this->headers['headers']['CI_VIEW_SOURCE_DOMAIN'] = 'beta1.ux';
        $this->headers['headers']['CI_API_SOURCE_DOMAIN'] = 'beta1.ux';
        $this->headers['headers']['CI_INSIDE_SOURCE_DOMAIN'] = 'beta1.ux';
        $this->headers['headers']['CI_FIXED_SOURCE_DOMAIN'] = 'beta1.ux';

        return $this->headers;
    }

    public function preview()
    {
        $this->headers['proxy'] = '10.1.24.249:8080';
        $this->headers['headers']['CI_VIEW_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_API_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_INSIDE_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_FIXED_SOURCE_DOMAIN'] = 'uitox.com';

        return $this->headers;
    }

    public function online()
    {
        $this->headers['proxy'] = '10.1.24.98:8080';
        $this->headers['headers']['CI_VIEW_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_API_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_INSIDE_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_FIXED_SOURCE_DOMAIN'] = 'uitox.com';

        return $this->headers;
    }
}
?>