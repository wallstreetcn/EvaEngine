<?php
/**
 * EvaEngine (http://evaengine.com/)
 * A development engine based on Phalcon Framework.
 *
 * @copyright Copyright (c) 2014-2015 EvaEngine Team (https://github.com/EvaEngine/EvaEngine)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Eva\EvaEngine\Service;

use Eva\EvaEngine\Exception\OriginNotAllowedException;
use Phalcon\DI\InjectionAwareInterface;

class Cors implements InjectionAwareInterface
{

    protected $_di;

    protected $config;
    
    public function __construct($config)
    {
        $this->setConfig($config);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function setDI($di)
    {
        $this->_di = $di;
    }

    public function getDI()
    {
        return $this->_di;
    }

    public function simpleRequests()
    {
        $httpOrigin = $this->getDI()->getRequest()->getHeader('HTTP_ORIGIN');
        if (empty($httpOrigin)) {
            return;
        }
        if (! $this->isHttpOriginAllowed()) {
            return;
        }
        $this->getDI()->getResponse()->setHeader('Access-Control-Allow-Origin', $httpOrigin);
    }

    public function preflightRequests(
        $allowCredentials = 'true',
        $allowMethods = 'GET, POST, PUT, DELETE, OPTIONS',
        $allowHeaders = 'Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With'
    ) {
        $httpOrigin = $this->getDI()->getRequest()->getHeader('HTTP_ORIGIN');
        if (empty($httpOrigin)) {
            return;
        }

        if (! $this->isHttpOriginAllowed()) {
            return;
        }
        $this->getDI()->getResponse()->setHeader('Access-Control-Allow-Credentials', (string)$allowCredentials);
        $this->getDI()->getResponse()->setHeader('Access-Control-Allow-Origin', $httpOrigin);
        $this->getDI()->getResponse()->setHeader('Access-Control-Allow-Methods', $allowMethods);
        $this->getDI()->getResponse()->setHeader('Access-Control-Allow-Headers', $allowHeaders);
        if (strtoupper($this->getDI()->getRequest()->getMethod()) == 'OPTIONS') {
            $this->getDI()->getResponse()->send();
            return;
        }
    }

    protected function isHttpOriginAllowed()
    {
        $checked = false;
        $origin = parse_url($this->getDI()->getRequest()->getHeader('HTTP_ORIGIN'), PHP_URL_HOST);

        if ($this->isSameOrigin(
            $this->getDI()->getRequest()->getHeader('HTTP_HOST'),
            parse_url($this->getDI()->getRequest()->getHeader('HTTP_ORIGIN'), PHP_URL_HOST)
        )) {
            return true;
        }

        $this->config = array_merge([
            [
                'domain' => $this->getDI()->getRequest()->getHeader('HTTP_HOST')
            ]
        ], $this->config);

        foreach ($this->config as $domain) {
            $domainWithDot = '.' . ltrim($domain['domain'], '.');
            if ($origin === $domain['domain'] or ends_with($origin, $domainWithDot)) {
                $checked = true;
            }
        }
        return $checked;
    }

    private function isSameOrigin($origin, $domain)
    {
        $domainWithDot = '.' . ltrim($domain, '.');
        if ($origin === $domain or ends_with($origin, $domainWithDot)) {
            return true;
        }
        return false;
    }
}
