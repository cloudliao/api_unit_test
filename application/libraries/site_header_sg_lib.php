<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Site header and Proxy Setting
 *
 * @author brian.wang
 */
class Site_header_sg_lib
{
    private $headers = array(
        'proxy' => '',
        'headers' => array (
            "VIEW_COUNTRY"              => "sg",
            "VIEW_LANGUAGE"             => "en-sg",
            "VIEW_CURRENCY"             => "SGD",
            "VIEW_COUNTRY_REGION"       => "sg",
            "VIEW_BC_CODE"              => "B",
            "VIEW_TIMEZONE"             => "Asia/Singapore",
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