<?php
/**
 * BSD 2-Clause License
 *
 * Copyright (c) 2018 Jeroen Steggink
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

// TODO Add a way to set select or update type, this will choose shard leaders or any of the nodes
// TODO Load balancing in the Endpoint?
/**
 * Class for describing a SolrCloud abstract endpoint.
 * @package Solarium\Cloud\Core\Client
 */
abstract class AbstractEndpoint extends Configurable implements EndpointInterface
{
    /**
     * Default options.
     *
     * The defaults match a standard Solr example instance as distributed by
     * the Apache Lucene Solr project.
     *
     * @var array
     */
    protected $options = array(
        'timeout' => 5,
    );

    /**
     * AbstractEndpoint constructor.
     * @param array|\Zend_Config        $options
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function __construct(array $options = null)
    {
        parent::__construct($options);
    }

    /**
     * Set HTTP basic auth settings.
     *
     * If one or both values are NULL authentication will be disabled
     *
     * @param string $username
     * @param string $password
     *
     * @return AbstractEndpoint Provides fluent interface
     */
    public function setAuthentication($username, $password): AbstractEndpoint
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
    public function getAuthentication(): array
    {
        return array(
            'username' => $this->getOption('username'),
            'password' => $this->getOption('password'),
        );
    }

    /**
     * Get random Solr URL.
     *
     * @return string
     */
    public function getSolrUrl(): string
    {
        $solrurls = $this->getOption('solrurls');
        return $solrurls[array_rand($solrurls)];
    }

    /**
     * Get all Solr URLs.
     *
     * @return string[]
     */
    public function getSolrUrls(): array
    {
        return $this->getOption('solrurls');
    }

    /**
     * Get the base URI for all requests.
     *
     * Based on a random Solr URL from all URLs.
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->getServerUri();
    }

    /**
     * Get the server uri
     *
     * @return string
     */
    public function getServerUri(): string
    {
        return rtrim($this->getSolrUrl(), '/').'/';
    }

    /**
     * Get Solr timeout option.
     *
     * @return string
     */
    public function getTimeout(): string
    {
        return $this->getOption('timeout');
    }

    /**
     * Set Solr timeout option.
     *
     * @param int $timeout
     *
     * @return Configurable Provides fluent interface
     */
    public function setTimeout($timeout): Configurable
    {
        return $this->setOption('timeout', $timeout);
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
        return __CLASS__.'::__toString'."\n".'base uri: '.$this->getBaseUri()."\n".'collection: '.$this->getCollection()."\n".'timeout: '.$this->getTimeout()."\n".'authentication: '.print_r($this->getAuthentication(), 1);
    }

}
