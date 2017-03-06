<?php
/**
 * BSD 2-Clause License
 *
 * Copyright (c) 2017 Jeroen Steggink
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 *  Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Solarium\Cloud\Core\Client;

use Solarium\Core\Configurable;


// TODO is not complete, add methods to get information from state and update state
/**
 * Class for describing a SolrCloud endpoint.
 * @package Solarium\Cloud\Core\Client
 */
class CollectionEndpoint extends Configurable
{
    /** @var  string Name of the collection */
    protected $collection;
    /** @var  array Collection state retrieved from ZkStateReader */
    protected $state;

    /**
     * Default options.
     *
     * The defaults match a standard SolrCloud example instance as distributed by
     * the Apache Lucene Solr project.
     *
     * @var array
     */
    protected $options = array(
        'scheme'  => 'http',
        'host'    => '127.0.0.1',
        'port'    => 8983,
        'path'    => '/solr',
        'collection'    => null,
        'timeout' => 5,
    );

    /**
     * CollectionEndpoint constructor.
     * @param string             $collection
     * @param array|\Zend_Config $options
     */
    public function __construct(string $collection, array $options = null)
    {
        $this->collection = $collection;
        parent::__construct($options);
    }

    /**
     * Get key value.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getOption('key');
    }

    /**
     * Set key value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setKey($value)
    {
        return $this->setOption('key', $value);
    }

    /**
     * Set host option.
     *
     * @param string $host This can be a hostname or an IP address
     *
     * @return self Provides fluent interface
     */
    public function setHost($host)
    {
        return $this->setOption('host', $host);
    }

    /**
     * Get host option.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->getOption('host');
    }

    /**
     * Set port option.
     *
     * @param int $port Common values are 80, 8080 and 8983
     *
     * @return self Provides fluent interface
     */
    public function setPort($port)
    {
        return $this->setOption('port', $port);
    }

    /**
     * Get port option.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->getOption('port');
    }

    /**
     * Set path option.
     *
     * If the path has a trailing slash it will be removed.
     *
     * @param string $path
     *
     * @return self Provides fluent interface
     */
    public function setPath($path)
    {
        if (substr($path, -1) == '/') {
            $path = substr($path, 0, -1);
        }

        return $this->setOption('path', $path);
    }

    /**
     * Get path option.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getOption('path');
    }

    /**
     * Set collection option.
     *
     * @param string $collection
     *
     * @return self Provides fluent interface
     */
    public function setCollection($collection)
    {
        return $this->setOption('collection', $collection);
    }

    /**
     * Get collection option.
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->getOption('collection');
    }

    /**
     * Set timeout option.
     *
     * @param int $timeout
     *
     * @return self Provides fluent interface
     */
    public function setTimeout($timeout)
    {
        return $this->setOption('timeout', $timeout);
    }

    /**
     * Get timeout option.
     *
     * @return string
     */
    public function getTimeout()
    {
        return $this->getOption('timeout');
    }

    /**
     * Set scheme option.
     *
     * @param string $scheme
     *
     * @return self Provides fluent interface
     */
    public function setScheme($scheme)
    {
        return $this->setOption('scheme', $scheme);
    }

    /**
     * Get scheme option.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->getOption('scheme');
    }

    /**
     * Get the base url for all requests.
     *
     * Based on host, path, port and core options.
     *
     * @return string
     */
    public function getBaseUri()
    {
        $uri = $this->getScheme().'://'.$this->getHost().':'.$this->getPort().$this->getPath().'/';

        $collection = $this->getCollection();
        if (!empty($collection)) {
            $uri .= $collection.'/';
        }

        return $uri;
    }

    /**
     * Set HTTP basic auth settings.
     *
     * If one or both values are NULL authentication will be disabled
     *
     * @param string $username
     * @param string $password
     *
     * @return self Provides fluent interface
     */
    public function setAuthentication($username, $password)
    {
        $this->setOption('username', $username);
        $this->setOption('password', $password);

        return $this;
    }

    /**
     * Get HTTP basic auth settings.
     *
     * @return array
     */
    public function getAuthentication()
    {
        return array(
            'username' => $this->getOption('username'),
            'password' => $this->getOption('password'),
        );
    }

    /**
     * Magic method enables a object to be transformed to a string.
     *
     * Get a summary showing significant variables in the object
     * note: uri resource is decoded for readability
     *
     * @return string
     */
    public function __toString()
    {
        $output = __CLASS__.'::__toString'."\n".'base uri: '.$this->getBaseUri()."\n".'host: '.$this->getHost()."\n".'port: '.$this->getPort()."\n".'path: '.$this->getPath()."\n".'collection: '.$this->getCollection()."\n".'timeout: '.$this->getTimeout()."\n".'authentication: '.print_r($this->getAuthentication(), 1);

        return $output;
    }

    /**
     * Initialization hook.
     *
     * In this case the path needs to be cleaned of trailing slashes.
     *
     * @see setPath()
     */
    protected function init()
    {
        //TODO check if minimum fields are set

        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'path':
                    $this->setPath($value);
                    break;
            }
        }
    }
}