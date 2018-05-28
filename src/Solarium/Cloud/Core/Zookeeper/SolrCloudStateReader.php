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

namespace Solarium\Cloud\Core\Zookeeper;

use Psr\Cache\InvalidArgumentException;
use Solarium\Cloud\Core\Client\CloudClient;
use Solarium\Cloud\Core\Client\CollectionEndpoint;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class SolrCloudStateReader implements StateReaderInterface
{
    //TODO we can have multiple collection states
    /**
     * @var ClusterState
     */
    protected $clusterState;

    /** @var  AdapterInterface Cache object holding collection state information */
    private $cache;

    /**
     * SolrStateReader constructor.
     * @param CloudClient $client
     */
    public function __construct(CloudClient $client)
    {
        if (!$this->getCacheData()) {
            $query = $client->createCollectionsAPI();
            $query->getClusterStatus();
            $result = $client->collections($query);
            $this->clusterState = $result->getClusterStatus();
        }
    }

    public function getCollectionAliases(): array
    {
        return $this->clusterState->getAliases();
    }

    public function getCollectionList(): array
    {
        return $this->clusterState->getCollections();
    }

    public function getClusterState(): array
    {
        return $this->clusterState;
    }

    public function getClusterProperties(): array
    {
        // TODO: Implement getClusterProperties() method.
        return [];
    }

    public function getLiveNodes(): array
    {
        // TODO: Implement getLiveNodes() method.
        return [];
    }

    public function getActiveBaseUris(string $collection = null): array
    {
        // TODO: Implement getActiveBaseUris() method.
        return [];
    }

    public function getCollectionShardLeadersBaseUri(string $collection): array
    {
        // TODO: Implement getCollectionShardLeadersBaseUri() method.
        return [];
    }

    public function getEndpoints(): array
    {
        // TODO: Implement getEndpoints() method.
        return [];
    }

    public function getCollectionEndpoint(string $collection): CollectionEndpoint
    {
        // TODO: Implement getCollectionEndpoint() method.
        return [];
    }

    /**
     * Returns the official collection name
     * @param  string $collection Collection name
     * @return string Name of the collection. Returns an empty string if it's not found.
     */
    public function getCollectionName(string $collection): string
    {
        // TODO: Implement getCollectionName() method.
        return [];
    }

    /**
     * @return AdapterInterface
     */
    public function getCache(): AdapterInterface
    {
        return $this->cache;
    }

    /**
     * @param AdapterInterface $cache
     */
    public function setCache(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Check if all the data is in cache
     * @return bool
     */
    protected function getCacheData(): bool
    {
        try {
            if ($this->cache !== null) {

                if (!$this->cache->getItem('solrstate.collectionStates')->isHit()) {
                    return false;
                } else {
                    $this->clusterState = $this->cache->getItem('solrstate.collectionStates')->get();
                }

                return true;
            }
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return false;
    }

    /**
     * Updates the cache object
     *
     * @param int|Date
     * @return bool Returns whether storing data to cache was successful or not.
     */
    protected function fillCacheData($cacheExpiration = null): bool
    {
        if ($this->cache !== null) {
            try {
                $this->cache->save($this->cache->getItem('solrstate.aliases')->set($this->aliases)->expiresAfter($cacheExpiration));
                $this->cache->save($this->cache->getItem('solrstate.collections')->set($this->collections)->expiresAfter($cacheExpiration));
                $this->cache->save($this->cache->getItem('solrstate.legacyCollectionStates')->set($this->legacyCollectionStates)->expiresAfter($cacheExpiration));
                $this->cache->save($this->cache->getItem('solrstate.collectionStates')->set($this->collectionStates)->expiresAfter($cacheExpiration));
                $this->cache->save($this->cache->getItem('solrstate.clusterState')->set($this->clusterState)->expiresAfter($cacheExpiration));
                $this->cache->save($this->cache->getItem('solrstate.collectionShardLeaders')->set($this->collectionShardLeaders)->expiresAfter($cacheExpiration));
                $this->cache->save($this->cache->getItem('solrstate.liveNodes')->set($this->liveNodes)->expiresAfter($cacheExpiration));
                $this->cache->save($this->cache->getItem('solrstate.clusterProperties')->set($this->clusterProperties)->expiresAfter($cacheExpiration));
                $this->cache->save($this->cache->getItem('solrstate.securityData')->set($this->securityData)->expiresAfter($cacheExpiration));
                $this->cache->save($this->cache->getItem('solrstate.securityData')->set($this->securityData)->expiresAfter($cacheExpiration));
            } catch (InvalidArgumentException $e) {
                return false;
            }
        }

        return true;
    }
}