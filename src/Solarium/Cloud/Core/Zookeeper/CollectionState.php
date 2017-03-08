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

namespace Solarium\Cloud\Core\Zookeeper;

// TODO is not complete, add methods to get information from state and update state
/**
 * Class for describing a SolrCloud collection endpoint.
 * @package Solarium\Cloud\Core\Client
 */
class CollectionState extends AbstractState
{
    /** @var  string Name of the collection */
    protected $name;
    /** @var  ShardState[] */
    protected $shards;
    /** @var  array Array of aliases for collection */
    protected $aliases;

    /**
     * Name of the collection
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Magic method enables a object to be transformed to a string.
     *
     * Get a summary showing significant variables in the object
     *
     * @return string
     */
    public function __toString(): string
    {
        $output = __CLASS__.'::__toString'."\n".print_r($this->state, true);

        return $output;
    }

    /**
     * @return bool
     */
    public function isAutoAddReplicas(): bool
    {
        return $this->getState()[ZkStateReader::AUTO_ADD_REPLICAS];
    }

    /**
     *
     * @return int
     */
    public function getMaxShardsPerNode(): int
    {
        return $this->getState()[ZkStateReader::MAX_SHARDS_PER_NODE];
    }

    /**
     *
     * @return int
     */
    public function getReplicationFactor(): int
    {
        return $this->getState()[ZkStateReader::REPLICATION_FACTOR];
    }

    /**
     *
     * @return string
     */
    public function getRouterName(): string
    {
        return $this->getState()[ZkStateReader::ROUTER_PROP]['name'];
    }

    /**
     * Return all shards
     *
     * @return ShardState[]
     */
    public function getShards(): array
    {
        return $this->shards;
    }

    /**
     *
     * @return ReplicaState[]
     */
    public function getShardLeaders(): array
    {
        $leaders = array();
        foreach ($this->shards as $shardName => $shard) {
            $leaders[$shardName] = $shard->getShardLeader();
        }

        return $leaders;
    }

    /**
     * Array with shard names as keys and base urls as values.
     * @return string[]
     */
    public function getShardLeadersBaseUrls(): array
    {
        $urls = array();

        foreach ($this->getShards() as $shardName => $shard) {
            $urls[$shardName] = $shard->getShardLeaderBaseUrl();
        }

        return $urls;
    }

    /**
     * Array with node names as keys and base urls as values.
     *
     * @return string[]
     */
    public function getNodesBaseUrls(): array
    {
        $urls = array();

        foreach ($this->getShards() as $shard) {
            $urls = array_merge($shard->getNodesBaseUrls(), $urls);
        }

        return $urls;
    }

    /**
     * Get state array without the collection name key
     *
     * @return array
     */
    protected function getState(): array
    {
        return $this->state[$this->name];
    }

    /**
     *
     */
    protected function setShards()
    {
        // Clear shards first
        $this->shards = array();
        foreach ($this->getState()[ZkStateReader::SHARDS_PROP] as $shardName => $shardState) {
            $this->shards[$shardName] = new ShardState(array($shardName => $shardState));
        }
    }

    protected function init()
    {
        $this->name = key($this->state);
        $this->setShards();
    }
}