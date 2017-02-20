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

use Solarium\Cloud\Core\Zookeeper\ZkStateReader;
use Solarium\Cloud\Exception\UnsupportedOperationException;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Core\Event\PostExecuteRequest as PostExecuteRequestEvent;
use Solarium\Plugin\Loadbalancer\Loadbalancer;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Cloud interface for interaction with SolrCloud.
 *
 * Example usage with default settings:
 * <code>
 * $options = array('zkhosts' => 'localhost:2181,localhost:2182');
 * $client = new \Solarium\Cloud\Client($options);
 * $client->setCollection('collection1');
 * $query = $client->createSelect();
 * $result = $client->select($query);
 * </code>
 */
class CloudClient extends Client
{
    protected $clientTimeout = 30000;
    protected $collection;

    protected $zkStateReader;
    protected $zkHosts;
    protected $zkTimeout = 10000;
    protected $directUpdatesToLeadersOnly;

    public function __construct($options = null, $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct($options);
    }

    /**
     * Initialization hook.
     */
    protected function init()
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = new EventDispatcher();
        }

        if(!array_key_exists('zkhosts', $this->options))
            throw new InvalidArgumentException('"zkhosts" option is not defined but is required');

        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'zkhosts':
                    $this->zkHosts = $value;
                    break;
                case 'collection':
                    $this->collection = $value;
                    break;
                case 'querytype':
                    $this->registerQueryTypes($value);
                    break;
                case 'plugin':
                    $this->registerPlugins($value);
                    break;
                case 'zktimeout':
                    $this->zkTimeout = $value;
                    break;
                case 'clienttimeout':
                    $this->clientTimeout = $value;
                    break;
            }
        }

        $this->zkStateReader = new ZkStateReader($this->zkHosts);
        $loadBalancerOptions = array('failoverenabled' => true);
        $this->registerPlugin('loadbalancer', new Loadbalancer($loadBalancerOptions));
    }

    /**
     * This method is unsupported in this client.
     */
    public function createEndpoint($options = null, $setAsDefault = false)
    {
        throw new UnsupportedOperationException("You cannot create an endpoint in the SolrCloud client.");
    }

    /**
     * This method is unsupported in this client.
     */
    public function addEndpoint($endpoint)
    {
        throw new UnsupportedOperationException("You cannot add an endpoint in the SolrCloud client.");
    }

    /**
     * This method is unsupported in this client.
     */
    public function addEndpoints(array $endpoints)
    {
        throw new UnsupportedOperationException("You cannot add endpoints in the SolrCloud client.");
    }

    /**
     * This method is unsupported in this client.
     */
    public function setEndpoints($endpoints)
    {
        throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
    }

    public function executeRequest($request, $endpoint = null)
    {
        if($endpoint != null)
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");

        if(empty($this->collection))
            throw new UnsupportedOperationException("No collection is specified.");

        $loadbalancer = $this->getPlugin('loadbalancer');

        // Set endpoints for collection
        $this->endpoints = $this->zkStateReader->getCollectionEndpoints($this->collection);
        $endpoints = array_keys($this->zkStateReader->getCollectionEndpoints($this->collection));
        $endpoints_loadbalancer = array();
        foreach($endpoints as $endpoint_id) {
            $endpoints_loadbalancer[$endpoint_id] = 1; // set weight to 1
        }
        $loadbalancer->setEndpoints($endpoints_loadbalancer);

        // Get first shard leader we can find as a default Endpoint
        $leaders = $this->zkStateReader->getCollectionShardLeadersEndpoints($this->collection);
        $endpoint = reset($leaders);
        $this->setDefaultEndpoint($endpoint);

        $event = new PreExecuteRequestEvent($request, $endpoint);
        $this->eventDispatcher->dispatch(Events::PRE_EXECUTE_REQUEST, $event);
        if ($event->getResponse() !== null) {
            $response = $event->getResponse(); //a plugin result overrules the standard execution result
        } else {
            $response = $this->getAdapter()->execute($request, $endpoint);
        }

        $this->eventDispatcher->dispatch(
            Events::POST_EXECUTE_REQUEST,
            new PostExecuteRequestEvent($request, $endpoint, $response)
        );

        return $response;
    }

    /**
     * @return mixed
     */
    public function getDefaultCollection()
    {
      return $this->getOption('defaultCollection');
    }

    /**
     * @param string $collection
     */
    public function setDefaultCollection(string $collection)
    {
        $this->setOption('defaultCollection', $collection);
    }

    /**
     * @return string
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * @param $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    public function getIdField()
    {
        return $this->getOption('idField');
    }

    public function setIdField(string $idField)
    {
        $this->setOption('idField', $idField);
    }

    /**
     * @return int
     */
    public function getClientTimeout(): int
    {
        return $this->clientTimeout;
    }

    /**
     * @param int $clientTimeout
     */
    public function setClientTimeout(int $clientTimeout)
    {
        $this->clientTimeout = $clientTimeout;
    }

    /**
     * @return int
     */
    public function getZkTimeout(): int
    {
        return $this->zkTimeout;
    }

    public function setZkTimeout(int $timeout)
    {
        $this->zkTimeout = $timeout;
    }

    /**
     * @return bool
     */
    public function isDirectUpdatesToLeadersOnly(): bool
    {
        return $this->directUpdatesToLeadersOnly;
    }

    /**
     *
     */
    public function sendDirectUpdatesToShardLeadersOnly()
    {
        $this->directUpdatesToLeadersOnly = true;
    }

    /**
     *
     */
    public function sendDirectUpdatesToAnyShardReplica()
    {
        $this->directUpdatesToLeadersOnly = false;
    }
}
