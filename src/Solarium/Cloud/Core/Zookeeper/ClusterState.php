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

use Solarium\Cloud\Core\Zookeeper\CollectionState;

class ClusterState
{
    /*
     * @var string[]
     */
    protected $aliases;
    /**
     * @var CollectionState[]
     */
    protected $collections;
    /**
     * @var string[]
     */
    protected $liveNodes;

    public function __construct(array $clusterStatus)
    {
        $this->aliases = $clusterStatus['aliases'];
        foreach ($clusterStatus['collections'] as $collectionName => $collectionState) {
            $this->collections[$collectionName] = new CollectionState([$collectionName => $collectionState], $clusterStatus['live_nodes']);
        }
        $this->liveNodes = $clusterStatus['live_nodes'];
    }

    /**
     * @return string[]
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @return ClusterState[]
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    /**
     * @param string $collectionName
     * @return \Solarium\Cloud\Core\Zookeeper\CollectionState
     */
    public function getCollection(string $collectionName): \Solarium\Cloud\Core\Zookeeper\CollectionState
    {
        return $this->collections[$collectionName];
    }

    /**
     * @return string[]
     */
    public function getLiveNodes()
    {
        return $this->liveNodes;
    }


}