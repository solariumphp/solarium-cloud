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
use Solarium\Exception\InvalidArgumentException;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Core\Event\PostExecuteRequest as PostExecuteRequestEvent;
use Solarium\Core\Query\QueryInterface;
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
    /* @var int */
    protected $clientTimeout = 30000;
    /* @var string */
    protected $collection;
    /* @var ZkStateReader */
    protected $zkStateReader;
    /* @var string[] */
    protected $zkHosts;
    /* @var int */
    protected $zkTimeout = 10000;
    /* @var bool */
    protected $directUpdatesToLeadersOnly;
    /* @var bool */
    protected $failoverEnabled = true;
    /* @var bool */
    protected $loadBalancingEnabled = true;

    /**
     * CloudClient constructor.
     * @param null $options
     * @param null $eventDispatcher
     */
    public function __construct($options = null, $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct($options);
    }

    /**
     * This method is unsupported in this client.
     * @throws UnsupportedOperationException
     */
    public function createEndpoint($options = null, $setAsDefault = false)
    {
        throw new UnsupportedOperationException("You cannot create an endpoint in the SolrCloud client.");
    }

    /**
     * This method is unsupported in this client.
     * @throws UnsupportedOperationException
     */
    public function addEndpoint($endpoint)
    {
        throw new UnsupportedOperationException("You cannot add an endpoint in the SolrCloud client.");
    }

    /**
     * This method is unsupported in this client.
     * @throws UnsupportedOperationException
     */
    public function addEndpoints(array $endpoints)
    {
        throw new UnsupportedOperationException("You cannot add endpoints in the SolrCloud client.");
    }

    /**
     * This method is unsupported in this client.
     * @throws UnsupportedOperationException
     */
    public function setEndpoints($endpoints)
    {
        throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
    }

    /**
     * This method is unsupported in this client.
     * @throws UnsupportedOperationException
     */
    public function setDefaultEndpoint($endpoint)
    {
        throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
    }

    /**
     * Execute a query.
     *
     * @param QueryInterface $query
     * @param Endpoint|null  $endpoint You cannot set endpoints in the SolrCloud client.
     *
     * @return ResultInterface
     */
    public function execute(QueryInterface $query, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        return parent::execute($query);
    }

    /**
     * @param Request $request
     * @param null    $endpoint You cannot set endpoints in the SolrCloud client.
     * @return \Solarium\Core\Client\Response
     */
    public function executeRequest($request, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        if (empty($this->collection)) {
            throw new UnsupportedOperationException("No collection is specified.");
        }

        $loadbalancer = $this->getPlugin('loadbalancer');

        // Set endpoints for collection
        $this->endpoints = $this->zkStateReader->getCollectionEndpoints($this->collection);
        $endpoints = array_keys($this->zkStateReader->getCollectionEndpoints($this->collection));
        foreach ($endpoints as $endpointId) {
            $endpointsLoadbalancer[$endpointId] = 1; // set weight to 1
        }
        //TODO add check if exists.
        $loadbalancer->setEndpoints($endpointsLoadbalancer);

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

    public function ping(QueryInterface $query, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        return parent::ping($query);
    }

    //TODO fix example with zkhosts
    /**
     * Execute an update query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Cloud\Client;
     * $query = $client->createUpdate();
     * $update->addOptimize();
     * $result = $client->update($update);
     * </code>
     *
     * @see Solarium\QueryType\Update
     * @see Solarium\Result\Update
     *
     * @param QueryInterface|\Solarium\QueryType\Update\Query\Query $query
     * @param Endpoint|null                                         $endpoint You cannot set endpoints in the SolrCloud client.
     *
     * @return \Solarium\QueryType\Update\Result
     */
    public function update(QueryInterface $query, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        return parent::update($query);
    }

    //TODO fix example with zkhosts
    /**
     * Execute a select query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Cloud\Client;
     * $query = $client->createSelect();
     * $result = $client->select($query);
     * </code>
     *
     * @see Solarium\QueryType\Select
     * @see Solarium\Result\Select
     *
     * @param QueryInterface|\Solarium\QueryType\Select\Query\Query $query
     * @param Endpoint|null                                         $endpoint You cannot set endpoints in the SolrCloud client.
     *
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function select(QueryInterface $query, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        return $this->execute($query);
    }


    //TODO fix example with zkhosts
    /**
     * Execute a MoreLikeThis query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createMoreLikeThis();
     * $result = $client->moreLikeThis($query);
     * </code>
     *
     * @see Solarium\QueryType\MoreLikeThis
     * @see Solarium\Result\MoreLikeThis
     *
     * @param QueryInterface|\Solarium\QueryType\MoreLikeThis\Query $query
     * @param Endpoint|null                                         $endpoint You cannot set endpoints in the SolrCloud client.
     *
     * @return \Solarium\QueryType\MoreLikeThis\Result
     */
    public function moreLikeThis(QueryInterface $query, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        return parent::moreLikeThis($query);
    }

    /**
     * Execute an analysis query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Analysis\Query\Document|\Solarium\QueryType\Analysis\Query\Field $query
     * @param Endpoint|null                                                                                       $endpoint You cannot set endpoints in the SolrCloud client.
     *
     * @return \Solarium\QueryType\Analysis\Result\Document|\Solarium\QueryType\Analysis\Result\Field
     */
    public function analyze(QueryInterface $query, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        return $this->execute($query);
    }

    /**
     * Execute a terms query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Terms\Query $query
     * @param Endpoint|null                                  $endpoint You cannot set endpoints in the SolrCloud client.
     *
     * @return \Solarium\QueryType\Terms\Result
     */
    public function terms(QueryInterface $query, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        return $this->execute($query);
    }

    /**
     * Execute a suggester query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Suggester\Query $query
     * @param Endpoint|null                                      $endpoint You cannot set endpoints in the SolrCloud client.
     *
     * @return \Solarium\QueryType\Suggester\Result\Result
     */
    public function suggester(QueryInterface $query, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        return $this->execute($query);
    }

    /**
     * Execute an extract query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Extract\Query $query
     * @param Endpoint|null                                    $endpoint You cannot set endpoints in the SolrCloud client.
     *
     * @return \Solarium\QueryType\Extract\Result
     */
    public function extract(QueryInterface $query, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        return $this->execute($query);
    }

    /**
     * Execute a RealtimeGet query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\RealtimeGet\Query $query
     * @param Endpoint|null                                        $endpoint You cannot set endpoints in the SolrCloud client.
     *
     * @return \Solarium\QueryType\RealtimeGet\Result
     */
    public function realtimeGet(QueryInterface $query, $endpoint = null)
    {
        if ($endpoint != null) {
            throw new UnsupportedOperationException("You cannot set endpoints in the SolrCloud client.");
        }

        return $this->execute($query);
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
     * @param string $collection
     */
    public function setCollection(string $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return mixed
     */
    public function getIdField(): string
    {
        return $this->getOption('idField');
    }

    /**
     * @param string $idField
     */
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

    /**
     * @param int $timeout
     */
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

    /**
     * Initialization hook.
     */
    protected function init()
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = new EventDispatcher();
        }

        if (array_key_exists('zkhosts', $this->options) === false) {
            throw new InvalidArgumentException('"zkhosts" option is not defined but is required');
        }

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
                case 'loadbalancingenabled':
                    $this->loadBalancingEnabled = $value;
                case 'failoverenabled':
                    $this->failoverEnabled = $value;
            }
        }

        $this->zkStateReader = new ZkStateReader($this->zkHosts);

        $loadBalancerOptions = array('failoverenabled' => $this->failoverEnabled);
        $this->registerPlugin('loadbalancer', new Loadbalancer($loadBalancerOptions));
    }

}
