<?php
/**
 * BSD 2-Clause License
 *
 * Copyright (c) 2017 Jeroen Steggink, Bas de Nooijer
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

use Solarium\Cloud\Core\Client\Adapter\AdapterInterface;
use Solarium\Cloud\Core\Zookeeper\StateReaderInterface;
use Solarium\Core\Client\Adapter;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
interface CloudClientInterface
{

    /**
     * @return string
     */
    public function getDefaultCollection(): string;

    /**
     * @param string $collection
     * @return CloudClientInterface Provides fluent interface
     */
    public function setDefaultCollection(string $collection): CloudClientInterface;

    /**
     * Get the collection
     * @return string
     */
    public function getCollection(): string;

    /**
     * Set the collection
     *
     * @param string $collection
     * @return CloudClientInterface Provides fluent interface
     */
    public function setCollection(string $collection): CloudClientInterface;

    /**
     * The the name of the id field
     * @return string
     */
    public function getIdField(): string;

    /**
     * Set the name of the id field
     * @param string $idField
     * @return CloudClientInterface Provides fluent interface
     */
    public function setIdField(string $idField): CloudClientInterface;

    /**
     * @return int
     */
    public function getQueryTimeout(): int;

    /**
     * @param int $queryTimeout
     * @return CloudClientInterface Provides fluent interface
     */
    public function setQueryTimeout(int $queryTimeout): CloudClientInterface;

    /**
     * Get update timeout
     * @return int
     */
    public function getUpdateTimeout(): int;

    /**
     * Set update timeout
     *
     * @param int $updateTimeout
     * @return CloudClientInterface Provides fluent interface
     */
    public function setUpdateTimeout(int $updateTimeout): CloudClientInterface;

    /**
     * Return optimize timeout
     *
     * @return int
     */
    public function getOptimizeTimeout(): int;

    /**
     * Set optimize timeout.
     *
     * @param int $optimizeTimeout
     * @return CloudClientInterface Provides fluent interface
     */
    public function setOptimizeTimeout(int $optimizeTimeout): CloudClientInterface;

    /**
     * Get state reader.
     *
     * @return StateReaderInterface
     */
    public function getStateReader(): StateReaderInterface;

    /**
     * Set state reader.
     *
     * @param StateReaderInterface $stateReader
     * @return CloudClientInterface Provides fluent interface
     */
    public function setStateReader(StateReaderInterface $stateReader): CloudClientInterface;

    /**
     * Get state reader client timeout.
     *
     * @return int
     */
    public function getStateReaderTimeout(): int;

    /**
     * Set state reader client timeout
     *
     * @param int $timeout
     * @return CloudClientInterface Provides fluent interface
     */
    public function setStateReaderTimeout(int $timeout): CloudClientInterface;

    /**
     * @return bool
     */
    public function isDirectUpdatesToLeadersOnly(): bool;

    /**
     * @return CloudClientInterface Provides fluent interface
     */
    public function sendDirectUpdatesToShardLeadersOnly(): CloudClientInterface;

    /**
     * @return CloudClientInterface Provides fluent interface
     */
    public function sendDirectUpdatesToAnyShardReplica(): CloudClientInterface;

    /**
     * Get the CollectionEndpoint for a specific collection.
     *
     * @param  string $collection Collection name
     * @return CollectionEndpoint
     */
    public function getEndpoint(string $collection): CollectionEndpoint;

    /**
     * Get all CollectionEndpoints.
     *
     * @return CollectionEndpoint[]
     */
    public function getEndpoints(): array;

    /**
     * Set the adapter
     *
     * The adapter has to be a class that implements the AdapterInterface
     *
     * If a string is passed it is assumed to be the classname and it will be
     * instantiated on first use. This requires the availability of the class
     * through autoloading or a manual require before calling this method.
     * Any existing adapter instance will be removed by this method, this way an
     * instance of the new adapter type will be created upon the next usage of
     * the adapter (lazy-loading)
     *
     * If an adapter instance is passed it will replace the current adapter
     * immediately, bypassing the lazy loading.
     *
     * @throws InvalidArgumentException
     * @param  string|Adapter\AdapterInterface $adapter
     * @return CloudClientInterface Provides fluent interface
     */
    public function setAdapter($adapter): CloudClientInterface;

    /**
     * Get the adapter instance
     *
     * If {@see $adapter} doesn't hold an instance a new one will be created by
     * calling {@see createAdapter()}
     *
     * @param  boolean $autoload
     * @return AdapterInterface
     */
    public function getAdapter($autoload = true): AdapterInterface;

    /**
     * Register a querytype
     *
     * You can also use this method to override any existing querytype with a new mapping.
     * This requires the availability of the classes through autoloading or a manual
     * require before calling this method.
     *
     * @param  string $type
     * @param  string $queryClass
     * @return CloudClientInterface   Provides fluent interface
     */
    public function registerQueryType($type, $queryClass): CloudClientInterface;

    /**
     * Register multiple querytypes
     *
     * @param  array $queryTypes
     * @return CloudClientInterface Provides fluent interface
     */
    public function registerQueryTypes($queryTypes): CloudClientInterface;

    /**
     * Get all registered querytypes
     *
     * @return array
     */
    public function getQueryTypes(): array;

    /**
     * Gets the event dispatcher.
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * Sets the event dispatcher.
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher);

    /**
     * Register a plugin
     *
     * You can supply a plugin instance or a plugin classname as string.
     * This requires the availability of the class through autoloading
     * or a manual require.
     *
     * @throws InvalidArgumentException
     * @param  string                 $key
     * @param  string|PluginInterface $plugin
     * @param  array                  $options
     * @return CloudClientInterface Provides fluent interface
     */
    public function registerPlugin($key, $plugin, array $options = array()): CloudClientInterface;

    /**
     * Register multiple plugins
     *
     * @param  array $plugins
     * @return CloudClientInterface Provides fluent interface
     */
    public function registerPlugins($plugins): CloudClientInterface;

    /**
     * Get all registered plugins
     *
     * @return PluginInterface[]
     */
    public function getPlugins(): array;

    /**
     * Get a plugin instance
     *
     * @throws OutOfBoundsException
     * @param  string  $key
     * @param  boolean $autocreate
     * @return PluginInterface|null
     */
    public function getPlugin($key, $autocreate = true);

    /**
     * Remove a plugin instance
     *
     * You can remove a plugin by passing the plugin key, or the plugin instance
     *
     * @param  string|PluginInterface $plugin
     * @return CloudClientInterface        Provides fluent interface
     */
    public function removePlugin($plugin): CloudClientInterface;

    /**
     * Creates a request based on a query instance
     *
     * @throws UnexpectedValueException
     * @param  QueryInterface $query
     * @return \Solarium\Core\Client\Request
     */
    public function createRequest(QueryInterface $query): \Solarium\Core\Client\Request;

    /**
     * Creates a result object
     *
     * @throws UnexpectedValueException;
     * @param  QueryInterface                 $query
     * @param  \Solarium\Core\Client\Response $response
     * @return ResultInterface
     */
    public function createResult(QueryInterface $query, \Solarium\Core\Client\Response $response): ResultInterface;

    /**
     * Execute a query
     *
     * @param  QueryInterface $query
     * @return ResultInterface
     */
    public function execute(QueryInterface $query): ResultInterface;

    /**
     * Execute a request and return the response.
     *
     * @param \Solarium\Core\Client\Request $request
     *
     * @return \Solarium\Core\Client\Response
     */
    public function executeRequest(\Solarium\Core\Client\Request $request): \Solarium\Core\Client\Response;

    /**
     * Execute a ping query
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createPing();
     * $result = $client->ping($query);
     * </code>
     *
     * @see Solarium\QueryType\Ping
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Ping\Query $query
     * @return \Solarium\QueryType\Ping\Result
     */
    public function ping(QueryInterface $query): \Solarium\QueryType\Ping\Result;

    /**
     * Execute an update query
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createUpdate();
     * $update->addOptimize();
     * $result = $client->update($update);
     * </code>
     *
     * @see Solarium\QueryType\Update
     * @see Solarium\Result\Update
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Update\Query\Query $query
     * @return \Solarium\QueryType\Update\Result
     */
    public function update(QueryInterface $query): \Solarium\QueryType\Update\Result;

    /**
     * Execute a select query
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createSelect();
     * $result = $client->select($query);
     * </code>
     *
     * @see Solarium\QueryType\Select
     * @see Solarium\Result\Select
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Select\Query\Query $query
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function select(QueryInterface $query): \Solarium\QueryType\Select\Result\Result;

    /**
     * Execute a MoreLikeThis query
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
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\MoreLikeThis\Query $query
     * @return \Solarium\QueryType\MoreLikeThis\Result
     */
    public function moreLikeThis(QueryInterface $query): \Solarium\QueryType\MoreLikeThis\Result;

    /**
     * Execute an analysis query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Analysis\Query\Document|\Solarium\QueryType\Analysis\Query\Field $query
     * @return \Solarium\QueryType\Analysis\Result\Document|\Solarium\QueryType\Analysis\Result\Field
     */
    public function analyze(QueryInterface $query);

    /**
     * Execute a terms query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Terms\Query $query
     * @return \Solarium\QueryType\Terms\Result
     */
    public function terms(QueryInterface $query): \Solarium\QueryType\Terms\Result;

    /**
     * Execute a suggester query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Suggester\Query $query
     * @return \Solarium\QueryType\Suggester\Result\Result
     */
    public function suggester(QueryInterface $query): \Solarium\QueryType\Suggester\Result\Result;

    /**
     * Execute an extract query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Extract\Query $query
     * @return \Solarium\QueryType\Extract\Result
     */
    public function extract(QueryInterface $query): \Solarium\QueryType\Extract\Result;

    /**
     * Execute a RealtimeGet query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\RealtimeGet\Query $query
     * @return \Solarium\QueryType\RealtimeGet\Result
     */
    public function realtimeGet(QueryInterface $query): \Solarium\QueryType\RealtimeGet\Result;

    /**
     * Create a query instance
     *
     * @throws InvalidArgumentException|UnexpectedValueException
     * @param  string $type
     * @param  array  $options
     * @return AbstractQuery
     */
    public function createQuery($type, $options = null): AbstractQuery;

    /**
     * Create a select query instance
     *
     * @param  mixed $options
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function createSelect($options = null): \Solarium\QueryType\Select\Query\Query;

    /**
     * Create a MoreLikeThis query instance
     *
     * @param  mixed $options
     * @return \Solarium\QueryType\MorelikeThis\Query
     */
    public function createMoreLikeThis($options = null): \Solarium\QueryType\MorelikeThis\Query;

    /**
     * Create an update query instance
     *
     * @param  mixed $options
     * @return \Solarium\QueryType\Update\Query\Query
     */
    public function createUpdate($options = null): \Solarium\QueryType\Update\Query\Query;

    /**
     * Create a ping query instance
     *
     * @param  mixed $options
     * @return \Solarium\QueryType\Ping\Query
     */
    public function createPing($options = null): \Solarium\QueryType\Ping\Query;

    /**
     * Create an analysis field query instance
     *
     * @param  mixed $options
     * @return \Solarium\QueryType\Analysis\Query\Field
     */
    public function createAnalysisField($options = null): \Solarium\QueryType\Analysis\Query\Field;

    /**
     * Create an analysis document query instance
     *
     * @param  mixed $options
     * @return \Solarium\QueryType\Analysis\Query\Document
     */
    public function createAnalysisDocument($options = null): \Solarium\QueryType\Analysis\Query\Document;

    /**
     * Create a terms query instance
     *
     * @param  mixed $options
     * @return \Solarium\QueryType\Terms\Query
     */
    public function createTerms($options = null): \Solarium\QueryType\Terms\Query;

    /**
     * Create a suggester query instance
     *
     * @param  mixed $options
     * @return \Solarium\QueryType\Suggester\Query
     */
    public function createSuggester($options = null): \Solarium\QueryType\Suggester\Query;

    /**
     * Create an extract query instance
     *
     * @param  mixed $options
     * @return \Solarium\QueryType\Extract\Query
     */
    public function createExtract($options = null): \Solarium\QueryType\Extract\Query;

    /**
     * Create a RealtimeGet query instance
     *
     * @param  mixed $options
     * @return \Solarium\QueryType\RealtimeGet\Query
     */
    public function createRealtimeGet($options = null): \Solarium\QueryType\RealtimeGet\Query;
}
