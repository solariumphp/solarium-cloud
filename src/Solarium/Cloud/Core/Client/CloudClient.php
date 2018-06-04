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

use Solarium\Cloud\Core\Zookeeper\SolrCloudStateReader;
use Solarium\Cloud\Core\Zookeeper\StateReaderInterface;
use Solarium\Cloud\Core\Zookeeper\ZkStateReader;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Configurable;
use Solarium\Core\Plugin\PluginInterface;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Cloud\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\Exception\UnexpectedValueException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Solarium\Cloud\Core\Event\Events;
use Solarium\Core\Event\PreCreateRequest as PreCreateRequestEvent;
use Solarium\Core\Event\PostCreateRequest as PostCreateRequestEvent;
use Solarium\Core\Event\PreCreateQuery as PreCreateQueryEvent;
use Solarium\Core\Event\PostCreateQuery as PostCreateQueryEvent;
use Solarium\Core\Event\PreCreateResult as PreCreateResultEvent;
use Solarium\Core\Event\PostCreateResult as PostCreateResultEvent;
use Solarium\Core\Event\PreExecute as PreExecuteEvent;
use Solarium\Core\Event\PostExecute as PostExecuteEvent;
use Solarium\Cloud\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Cloud\Core\Event\PostExecuteRequest as PostExecuteRequestEvent;
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
class CloudClient extends Configurable implements CloudClientInterface
{
    /**
     * Querytype select.
     */
    const QUERY_SELECT = 'select';

    /**
     * Querytype update.
     */
    const QUERY_UPDATE = 'update';

    /**
     * Querytype admin.
     */
    const QUERY_ADMIN = 'admin';

    /**
     * Querytype ping.
     */
    const QUERY_PING = 'ping';

    /**
     * Querytype morelikethis.
     */
    const QUERY_MORELIKETHIS = 'mlt';

    /**
     * Querytype analysis field.
     */
    const QUERY_ANALYSIS_FIELD = 'analysis-field';

    /**
     * Querytype analysis document.
     */
    const QUERY_ANALYSIS_DOCUMENT = 'analysis-document';

    /**
     * Querytype terms.
     */
    const QUERY_TERMS = 'terms';

    /**
     * Querytype suggester.
     */
    const QUERY_SUGGESTER = 'suggester';

    /**
     * Querytype extract.
     */
    const QUERY_EXTRACT = 'extract';

    /**
     * Querytype get.
     */
    const QUERY_REALTIME_GET = 'get';

    // TODO add specific SolrCloud queries

    /**
     * Collections API
     */
    const QUERY_ADMIN_COLLECTIONS = 'collections';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = array(
        'adapter' => '\Solarium\Cloud\Core\Client\Adapter\Curl',
    );

    /**
     * Querytype mappings.
     *
     * These can be customized using {@link registerQueryType()}
     */
    protected $queryTypes = [
        self::QUERY_SELECT => '\Solarium\QueryType\Select\Query\Query',
        self::QUERY_UPDATE => '\Solarium\QueryType\Update\Query\Query',
        self::QUERY_PING => '\Solarium\QueryType\Ping\Query',
        self::QUERY_MORELIKETHIS => '\Solarium\QueryType\MoreLikeThis\Query',
        self::QUERY_ANALYSIS_DOCUMENT => '\Solarium\QueryType\Analysis\Query\Document',
        self::QUERY_ANALYSIS_FIELD => '\Solarium\QueryType\Analysis\Query\Field',
        self::QUERY_TERMS => '\Solarium\QueryType\Terms\Query',
        self::QUERY_SUGGESTER => '\Solarium\QueryType\Suggester\Query',
        self::QUERY_EXTRACT => '\Solarium\QueryType\Extract\Query',
        self::QUERY_REALTIME_GET => '\Solarium\QueryType\RealtimeGet\Query',
        self::QUERY_ADMIN_COLLECTIONS => '\Solarium\Cloud\QueryType\Admin\Collections',
        /* TODO
        self::QUERY_ADMIN_CORES => 'Solarium\Cloud\QueryType\Admin\Cores',
        self::QUERY_ADMIN_COLLECTIONS => 'Solarium\Cloud\QueryType\Admin\Collections',
        self::QUERY_ADMIN_INFO => 'Solarium\Cloud\QueryType\Admin\Info',
        self::QUERY_ADMIN_CONFIGS => 'Solarium\Cloud\QueryType\Admin\Configs',
        self::QUERY_ADMIN_AUTHZ => 'Solarium\Cloud\QueryType\Admin\Authorization',
        self::QUERY_ADMIN_AUTHC => 'Solarium\Cloud\QueryType\Admin\Authentication',
        self::QUERY_ADMIN_ZK_PATH => 'Solarium\Cloud\QueryType\Admin\Zookeeper',
        self::QUERY_ADMIN_METRICS => 'Solarium\Cloud\QueryType\Admin\Metrics',
        */
    ];

    /**
     * Plugin types.
     *
     * @var array
     */
    protected $pluginTypes = [
        'postbigrequest' => 'Solarium\Plugin\PostBigRequest',
        'customizerequest' => 'Solarium\Plugin\CustomizeRequest\CustomizeRequest',
        // TODO //'parallelexecution' => 'Solarium\Plugin\ParallelExecution\ParallelExecution',
        // TODO //'bufferedadd' => 'Solarium\Plugin\BufferedAdd\BufferedAdd',
        // TODO //'prefetchiterator' => 'Solarium\Plugin\PrefetchIterator',
        'minimumscorefilter' => 'Solarium\Plugin\MinimumScoreFilter\MinimumScoreFilter',
    ];

    /**
     * EventDispatcher.
     *
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * Registered plugin instances.
     *
     * @var PluginInterface[]
     */
    protected $pluginInstances = array();

    /**
     * Adapter instance.
     *
     * If an adapter instance is set using {@link setAdapter()} this var will
     * contain a reference to that instance.
     *
     * In all other cases the adapter is lazy-loading, it will be instantiated
     * on first use by {@link getAdapter()} based on the 'adapter' entry in
     * {@link $options}. This option can be set using {@link setAdapter()}
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /* @var int */
    protected $queryTimeout = 30000;

    /* @var int */
    protected $updateTimeout = 120000;

    /* @var int */
    protected $optimizeTimeout = 120000;

    /* @var string Current collection*/
    protected $collection;

    /* @var string Default collection to fallback to if no specific collection is given */
    protected $defaultCollection;

    /* @var string[] */
    protected $solrUrls;

    /* @var string[] */
    protected $zkHosts;

    /* @var int */
    protected $zkTimeout = 10000;

    /* @var StateReaderInterface */
    protected $stateReader;

    /* @var bool */
    protected $directUpdatesToLeadersOnly;

    /**
     * CloudClient constructor.
     * @param null $options
     * @param null $eventDispatcher
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function __construct($options = null, $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct($options);
    }

    /**
     * Execute a request and return the response.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Solarium\Exception\HttpException
     */
    public function executeRequest(\Solarium\Core\Client\Request $request): \Solarium\Core\Client\Response
    {
        if (empty($this->collection)) {
            throw new UnexpectedValueException('No collection is specified.');
        }

        $endpoint = $this->stateReader->getCollectionEndpoint($this->collection);

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
     * Execute a request and return the response.
     *
     * @param AdminRequest $request
     *
     * @return Response
     *
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function executeAdminRequest(\Solarium\Cloud\Core\Client\AdminRequest $request): \Solarium\Core\Client\Response
    {
        $endpoint = new AdminEndpoint(array('solrurls' => $this->solrUrls));

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
     * {@inheritDoc}
     */
    public function getDefaultCollection(): string
    {
        return $this->defaultCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultCollection(string $collection): CloudClientInterface
    {
        $this->setOption('defaultcollection', $collection);
        $this->defaultCollection = $collection;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     *{@inheritDoc}
     */
    public function setCollection(string $collection): CloudClientInterface
    {
        $this->setOption('collection', $collection);
        $this->collection = $collection;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdField(): string
    {
        return $this->getOption('idField');
    }

    /**
     * {@inheritDoc}
     */
    public function setIdField(string $idField): CloudClientInterface
    {
        $this->setOption('idField', $idField);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryTimeout(): int
    {
        return $this->queryTimeout;
    }

    /**
     * {@inheritDoc}
     */
    public function setQueryTimeout(int $queryTimeout): CloudClientInterface
    {
        $this->queryTimeout = $queryTimeout;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdateTimeout(): int
    {
        return $this->updateTimeout;
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdateTimeout(int $updateTimeout): CloudClientInterface
    {
        $this->updateTimeout = $updateTimeout;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptimizeTimeout(): int
    {
        return $this->optimizeTimeout;
    }

    /**
     * {@inheritDoc}
     */
    public function setOptimizeTimeout(int $optimizeTimeout): CloudClientInterface
    {
        $this->optimizeTimeout = $optimizeTimeout;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getStateReaderTimeout(): int
    {
        return $this->zkTimeout;
    }

    /**
     * {@inheritDoc}
     */
    public function setStateReaderTimeout(int $timeout): CloudClientInterface
    {
        $this->zkTimeout = $timeout;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isDirectUpdatesToLeadersOnly(): bool
    {
        return $this->directUpdatesToLeadersOnly;
    }

    /**
     * {@inheritDoc}
     */
    public function sendDirectUpdatesToShardLeadersOnly(): CloudClientInterface
    {
        $this->directUpdatesToLeadersOnly = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function sendDirectUpdatesToAnyShardReplica(): CloudClientInterface
    {
        $this->directUpdatesToLeadersOnly = false;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function getEndpoint(string $collection): CollectionEndpoint
    {
        return $this->stateReader->getCollectionEndpoint($collection);
    }

    /**
     * {@inheritDoc}
     */
    public function getEndpoints(): array
    {
        return $this->stateReader->getEndpoints();
    }

    /**
     * {@inheritDoc}
     */
    public function getLeaderEndpoints(string $collection = null)
    {
        // TODO: Implement getLeaderEndpoints() method.
    }

    /**
     * {@inheritDoc}
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function setAdapter($adapter): CloudClientInterface
    {
        if (is_string($adapter)) {
            $this->adapter = null;
            $this->setOption('adapter', $adapter);
        } elseif ($adapter instanceof AdapterInterface) {
            // forward options
            $adapter->setOptions($this->getOption('adapteroptions'));
            // overwrite existing adapter
            $this->adapter = $adapter;
        } else {
            throw new InvalidArgumentException('Invalid adapter input for setAdapter');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function getAdapter($autoload = true): AdapterInterface
    {
        if (null === $this->adapter && $autoload) {
            $this->createAdapter();
        }

        return $this->adapter;
    }

    /**
     * {@inheritDoc}
     */
    public function registerQueryType($type, $queryClass): CloudClientInterface
    {
        $this->queryTypes[$type] = $queryClass;

        return $this;
    }

    /**
     * Register multiple querytypes
     *
     * @param  array $queryTypes
     * @return CloudClientInterface  Provides fluent interface
     */
    public function registerQueryTypes($queryTypes): CloudClientInterface
    {
        foreach ($queryTypes as $type => $class) {
            // support both "key=>value" and "(no-key) => array(key=>x,query=>y)" formats
            if (is_array($class)) {
                if (isset($class['type'])) {
                    $type = $class['type'];
                }
                $class = $class['query'];
            }

            $this->registerQueryType($type, $class);
        }

        return $this;
    }

    /**
     * Get all registered querytypes
     *
     * @return array
     */
    public function getQueryTypes(): array
    {
        return $this->queryTypes;
    }

    /**
     * Gets the event dispatcher.
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Sets the event dispatcher.
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return CloudClientInterface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): CloudClientInterface
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

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
    public function registerPlugin($key, $plugin, array $options = array()): CloudClientInterface
    {
        if (is_string($plugin)) {
            $plugin = class_exists($plugin) ? $plugin : $plugin.strrchr($plugin, '\\');
            $plugin = new $plugin();
        }

        if (!($plugin instanceof PluginInterface)) {
            throw new InvalidArgumentException('All plugins must implement the PluginInterface');
        }

        // TODO not sure how to fix PluginInterface, since it expects a Client input instead of CloudClient. It does work though, since we have the right methods.
        $plugin->initPlugin($this, $options);

        $this->pluginInstances[$key] = $plugin;

        return $this;
    }

    /**
     * Register multiple plugins
     *
     * @param  array $plugins
     * @return CloudClientInterface Provides fluent interface
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function registerPlugins($plugins): CloudClientInterface
    {
        foreach ($plugins as $key => $plugin) {
            if (!isset($plugin['key'])) {
                $plugin['key'] = $key;
            }

            $this->registerPlugin(
                $plugin['key'],
                $plugin['plugin'],
                $plugin['options']
            );
        }

        return $this;
    }

    /**
     * Get all registered plugins
     *
     * @return PluginInterface[]
     */
    public function getPlugins(): array
    {
        return $this->pluginInstances;
    }

    /**
     * Get a plugin instance
     *
     * @throws OutOfBoundsException
     * @param  string  $key
     * @param  boolean $autocreate
     * @return PluginInterface|null
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function getPlugin($key, $autocreate = true)
    {
        if (isset($this->pluginInstances[$key])) {
            return $this->pluginInstances[$key];
        }
        if ($autocreate) {
            if (array_key_exists($key, $this->pluginTypes)) {
                $this->registerPlugin($key, $this->pluginTypes[$key]);

                return $this->pluginInstances[$key];
            }
            throw new OutOfBoundsException('Cannot autoload plugin of unknown type: '.$key);
        }

        return null;
    }

    /**
     * Remove a plugin instance
     *
     * You can remove a plugin by passing the plugin key, or the plugin instance
     *
     * @param  string|PluginInterface $plugin
     * @return CloudClientInterface        Provides fluent interface
     */
    public function removePlugin($plugin): CloudClientInterface
    {
        if (is_object($plugin)) {
            foreach ($this->pluginInstances as $key => $instance) {
                if ($instance === $plugin) {
                    unset($this->pluginInstances[$key]);
                    break;
                }
            }
        } else {
            if (isset($this->pluginInstances[$plugin])) {
                unset($this->pluginInstances[$plugin]);
            }
        }

        return $this;
    }

    /**
     * Creates a request based on a query instance
     *
     * @throws UnexpectedValueException
     * @param  QueryInterface $query
     * @return AdminRequest
     */
    public function createRequest(QueryInterface $query): Request
    {
        $event = new PreCreateRequestEvent($query);
        $this->eventDispatcher->dispatch(Events::PRE_CREATE_REQUEST, $event);
        if ($event->getRequest() !== null) {
            return $event->getRequest();
        }

        $requestBuilder = $query->getRequestBuilder();
        if (!$requestBuilder || !($requestBuilder instanceof RequestBuilderInterface)) {
            throw new UnexpectedValueException('No requestbuilder returned by querytype: '.$query->getType());
        }

        $request = $requestBuilder->build($query);

        $this->eventDispatcher->dispatch(
            Events::POST_CREATE_REQUEST,
            new PostCreateRequestEvent($query, $request)
        );

        return $request;
    }

    /**
     * Creates a result object
     *
     * @param  QueryInterface $query
     * @param Response $response
     * @return ResultInterface
     */
    public function createResult(QueryInterface $query, \Solarium\Core\Client\Response $response): ResultInterface
    {
        $event = new PreCreateResultEvent($query, $response);
        $this->eventDispatcher->dispatch(Events::PRE_CREATE_RESULT, $event);
        if ($event->getResult() !== null) {
            return $event->getResult();
        }

        $resultClass = $query->getResultClass();
        $result = new $resultClass($query, $response);

        if (!($result instanceof ResultInterface)) {
            throw new UnexpectedValueException('Result class must implement the ResultInterface');
        }

        $this->eventDispatcher->dispatch(
            Events::POST_CREATE_RESULT,
            new PostCreateResultEvent($query, $response, $result)
        );

        return $result;
    }

    /**
     * Execute a query
     *
     * @param  QueryInterface $query
     * @return ResultInterface
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function execute(QueryInterface $query): ResultInterface
    {
        $event = new PreExecuteEvent($query);
        $this->eventDispatcher->dispatch(Events::PRE_EXECUTE, $event);
        if ($event->getResult() !== null) {
            return $event->getResult();
        }

        $request = $this->createRequest($query);
        $response = $this->executeRequest($request);
        $result = $this->createResult($query, $response);

        $this->eventDispatcher->dispatch(
            Events::POST_EXECUTE,
            new PostExecuteEvent($query, $result)
        );

        return $result;
    }

    /**
     * Execute an admin query.
     *
     * @param  QueryInterface $query
     * @return ResultInterface
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function executeAdmin(QueryInterface $query): ResultInterface
    {
        $event = new PreExecuteEvent($query);
        $this->eventDispatcher->dispatch(Events::PRE_EXECUTE, $event);
        if ($event->getResult() !== null) {
            return $event->getResult();
        }

        $request = $this->createRequest($query);
        $response = $this->executeAdminRequest($request);
        $result = $this->createResult($query, $response);

        $this->eventDispatcher->dispatch(
            Events::POST_EXECUTE,
            new PostExecuteEvent($query, $result)
        );

        return $result;
    }

    /**
     * Execute a ping query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
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
     * @param QueryInterface|\Solarium\QueryType\Ping\Query $query
     *
     * @return \Solarium\QueryType\Ping\Result
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function ping(QueryInterface $query): \Solarium\QueryType\Ping\Result
    {
        return $this->execute($query);
    }

    /**
     * Execute an update query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
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
     * @param QueryInterface|\Solarium\QueryType\Update\Query\Query $query
     *
     * @return \Solarium\QueryType\Update\Result
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function update(QueryInterface $query): \Solarium\QueryType\Update\Result
    {
        return $this->execute($query);
    }

    /**
     * Execute a select query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
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
     * @param QueryInterface|\Solarium\QueryType\Select\Query\Query $query
     *
     * @return \Solarium\QueryType\Select\Result\Result
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function select(QueryInterface $query): \Solarium\QueryType\Select\Result\Result
    {
        return $this->execute($query);
    }

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
     *
     * @return \Solarium\QueryType\MoreLikeThis\Result
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function moreLikeThis(QueryInterface $query): \Solarium\QueryType\MoreLikeThis\Result
    {
        return $this->execute($query);
    }

    /**
     * Execute an analysis query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Analysis\Query\Document|\Solarium\QueryType\Analysis\Query\Field $query
     *
     * @return \Solarium\QueryType\Analysis\Result\Document|\Solarium\QueryType\Analysis\Result\Field
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function analyze(QueryInterface $query)
    {
        return $this->execute($query);
    }

    /**
     * Execute a terms query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Terms\Query $query
     *
     * @return \Solarium\QueryType\Terms\Result
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function terms(QueryInterface $query): \Solarium\QueryType\Terms\Result
    {
        return $this->execute($query);
    }

    /**
     * Execute a suggester query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Suggester\Query $query
     *
     * @return \Solarium\QueryType\Suggester\Result\Result
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function suggester(QueryInterface $query): \Solarium\QueryType\Suggester\Result\Result
    {
        return $this->execute($query);
    }

    /**
     * Execute an extract query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Extract\Query $query
     *
     * @return \Solarium\QueryType\Extract\Result
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function extract(QueryInterface $query): \Solarium\QueryType\Extract\Result
    {
        return $this->execute($query);
    }

    /**
     * Execute a RealtimeGet query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\RealtimeGet\Query $query
     *
     * @return \Solarium\QueryType\RealtimeGet\Result
     * @throws \Solarium\Exception\HttpException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \Solarium\Exception\UnexpectedValueException
     */
    public function realtimeGet(QueryInterface $query): \Solarium\QueryType\RealtimeGet\Result
    {
        return $this->execute($query);
    }

    /**
     * Create a query instance
     *
     * @throws InvalidArgumentException|UnexpectedValueException
     * @param  string $type
     * @param  array  $options
     * @return AbstractQuery
     */
    public function createQuery($type, $options = null): AbstractQuery
    {
        $type = strtolower($type);

        $event = new PreCreateQueryEvent($type, $options);
        $this->eventDispatcher->dispatch(Events::PRE_CREATE_QUERY, $event);
        if ($event->getQuery() !== null) {
            return $event->getQuery();
        }

        if (!isset($this->queryTypes[$type])) {
            throw new InvalidArgumentException('Unknown querytype: '.$type);
        }

        $class = $this->queryTypes[$type];
        $query = new $class($options);

        if (!($query instanceof QueryInterface)) {
            throw new UnexpectedValueException('All query classes must implement the QueryInterface');
        }

        $this->eventDispatcher->dispatch(
            Events::POST_CREATE_QUERY,
            new PostCreateQueryEvent($type, $options, $query)
        );

        return $query;
    }

    /**
     * Create an admin query instance.
     *
     * @param mixed $options
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    //TODO does not exist yet
    /*public function createAdmin($options = null)//: \Solarium\QueryType\Admin\ClusterStatus
    {
        return $this->createQuery(self::QUERY_PING, $options);
    }*/

    /**
     * Create a select query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Select\Query\Query
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createSelect($options = null): \Solarium\QueryType\Select\Query\Query
    {
        return $this->createQuery(self::QUERY_SELECT, $options);
    }

    /**
     * Create a Collections API query.
     *
     * @param mixed $options
     *
     * @return \Solarium\Cloud\QueryType\Admin\Collections
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createCollectionsAPI($options = null): \Solarium\Cloud\QueryType\Admin\Collections
    {
        return $this->createQuery(self::QUERY_ADMIN_COLLECTIONS, $options);
    }

    /**
     * Create a MoreLikeThis query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\MorelikeThis\Query
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createMoreLikeThis($options = null): \Solarium\QueryType\MorelikeThis\Query
    {
        return $this->createQuery(self::QUERY_MORELIKETHIS, $options);
    }

    /**
     * Create an update query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Update\Query\Query
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createUpdate($options = null): \Solarium\QueryType\Update\Query\Query
    {
        return $this->createQuery(self::QUERY_UPDATE, $options);
    }

    /**
     * Create a ping query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Ping\Query
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createPing($options = null): \Solarium\QueryType\Ping\Query
    {
        return $this->createQuery(self::QUERY_PING, $options);
    }

    /**
     * Create an analysis field query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Analysis\Query\Field
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createAnalysisField($options = null): \Solarium\QueryType\Analysis\Query\Field
    {
        return $this->createQuery(self::QUERY_ANALYSIS_FIELD, $options);
    }

    /**
     * Create an analysis document query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Analysis\Query\Document
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createAnalysisDocument($options = null): \Solarium\QueryType\Analysis\Query\Document
    {
        return $this->createQuery(self::QUERY_ANALYSIS_DOCUMENT, $options);
    }

    /**
     * Create a terms query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Terms\Query
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createTerms($options = null): \Solarium\QueryType\Terms\Query
    {
        return $this->createQuery(self::QUERY_TERMS, $options);
    }

    /**
     * Create a suggester query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Suggester\Query
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createSuggester($options = null): \Solarium\QueryType\Suggester\Query
    {
        return $this->createQuery(self::QUERY_SUGGESTER, $options);
    }

    /**
     * Create an extract query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Extract\Query
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createExtract($options = null): \Solarium\QueryType\Extract\Query
    {
        return $this->createQuery(self::QUERY_EXTRACT, $options);
    }

    /**
     * Create a RealtimeGet query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\RealtimeGet\Query
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createRealtimeGet($options = null): \Solarium\QueryType\RealtimeGet\Query
    {
        return $this->createQuery(self::QUERY_REALTIME_GET, $options);
    }

    /**
     * Create an Collections API query.
     *
     * @param mixed $options
     *
     * @return \Solarium\Cloud\Component\Admin\Collections\Query\ClusterStatus
     * @throws \Solarium\Exception\UnexpectedValueException
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function createCollectionsClusterStatus($options = null): \Solarium\Cloud\Component\Admin\Collections\Query\ClusterStatus
    {
        return $this->createQuery(self::QUERY_COLLECTIONS_CLUSTERSTATUS, $options);
    }

    /**
     * Execute a Collections API query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createSelect();
     * $result = $client->select($query);
     * </code>
     *
     * @see \Solarium\Cloud\QueryType\Admin\Collections
     *
     * @param QueryInterface|\Solarium\QueryType\Select\Query\Query $query
     *
     * @return \Solarium\Cloud\QueryType\Admin\Collections\Result
     */
    public function collections(QueryInterface $query)
    {
        return $this->executeAdmin($query);
    }

    /**
     * Get state reader.
     *
     * @return StateReaderInterface
     */
    public function getStateReader(): StateReaderInterface
    {
        return $this->stateReader;
    }

    /**
     * Set state reader.
     *
     * @param StateReaderInterface $stateReader
     * @return CloudClientInterface Provides fluent interface
     */
    public function setStateReader(StateReaderInterface $stateReader): CloudClientInterface
    {
        $this->stateReader = $stateReader;

        return $this;
    }

    /**
     * Initialization hook.
     * @throws \Solarium\Cloud\Exception\ZookeeperException
     * @throws \Solarium\Exception\InvalidArgumentException
     * @throws \ZookeeperConnectionException
     */
    protected function init()
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = new EventDispatcher();
        }

        if (array_key_exists('zkhosts', $this->options) === false && array_key_exists('solrurls', $this->options) === false) {
            throw new InvalidArgumentException('"zkhosts" and "solrurls" options are not defined, one of them is required.');
        }
        /*
        if (array_key_exists('defaultcollection', $this->options) === false) {
            throw new InvalidArgumentException('"defaultcollection" option is not defined but is required');
        }
        */

        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'solrurls':
                    $this->solrUrls = explode(',', $value);
                    break;
                case 'zkhosts':
                    $this->zkHosts = explode(',', $value);
                    break;
                case 'collection':
                    $this->collection = $value;
                    break;
                case 'defaultcollection':
                    $this->defaultCollection = $value;
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
                case 'querytimeout':
                    $this->queryTimeout = $value;
                    break;
                case 'updatetimeout':
                    $this->updateTimeout = $value;
                    break;
                case 'optimizetimeout':
                    $this->optimizeTimeout = $value;
                    break;
            }
        }

        if(!empty($this->zkHosts)) {
            $this->stateReader = new ZkStateReader($this->zkHosts);
        }
        elseif(!empty($this->solrUrls)) {
            $this->stateReader = new SolrCloudStateReader($this);
        }
    }

    /**
     * {@inheritDoc}
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    protected function createAdapter()
    {
        $adapterClass = $this->getOption('adapter');
        $adapter = new $adapterClass();

        // check interface
        if (!($adapter instanceof AdapterInterface)) {
            throw new InvalidArgumentException('An adapter must implement the AdapterInterface');
        }

        $adapter->setOptions($this->getOption('adapteroptions'));
        $this->adapter = $adapter;
    }
}
