<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Site header and Proxy Setting
 *
 * @author brian.wang
 */
class Site_header_tw_lib
{
    private $headers = array(
        'proxy' => '',
        'headers' => array (
            "VIEW_COUNTRY"              => "tw",
            "VIEW_LANGUAGE"             => "zh-tw",
            "VIEW_CURRENCY"             => "TWD",
            "VIEW_COUNTRY_REGION"       => "tw",
            "VIEW_BC_CODE"              => "A",
            "VIEW_TIMEZONE"             => "Asia/Taipei",
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

        return $this->headers;
    }

    public function beta()
    {
        $this->headers['headers']['CI_VIEW_SOURCE_DOMAIN'] = 'beta1.ux';
        $this->headers['headers']['CI_API_SOURCE_DOMAIN'] = 'beta1.ux';
        $this->headers['headers']['CI_INSIDE_SOURCE_DOMAIN'] = 'beta1.ux';
        $this->headers['headers']['CI_FIXED_SOURCE_DOMAIN'] = 'beta1.ux';

        return $this->headers;
    }

    public function preview()
    {
        $this->headers['headers']['CI_VIEW_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_API_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_INSIDE_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_FIXED_SOURCE_DOMAIN'] = 'uitox.com';

        return $this->headers;
    }

    public function online()
    {
        $this->headers['headers']['CI_VIEW_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_API_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_INSIDE_SOURCE_DOMAIN'] = 'uitox.com';
        $this->headers['headers']['CI_FIXED_SOURCE_DOMAIN'] = 'uitox.com';

        return $this->headers;
    }
}
?>