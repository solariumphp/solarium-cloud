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

use Solarium\Cloud\Core\Client\CollectionEndpoint;
use Symfony\Component\Cache\Adapter\AdapterInterface;

interface StateReaderInterface
{
    public function getCollectionAliases(): array;

    public function getCollectionList(): array;

    public function getClusterState(): array;

    public function getClusterProperties(): array;

    public function getLiveNodes(): array;

    public function getActiveBaseUris(string $collection = null): array;

    public function getCollectionShardLeadersBaseUri(string $collection): array;

    public function getEndpoints(): array;

    public function getCollectionEndpoint(string $collection): CollectionEndpoint;

    /**
     * Returns the official collection name
     * @param  string $collection Collection name
     * @return string Name of the collection. Returns an empty string if it's not found.
     */
    public function getCollectionName(string $collection): string;

    /**
     * @return AdapterInterface
     */
    public function getCache(): AdapterInterface;

    /**
     * @param AdapterInterface $cache
     */
    public function setCache(AdapterInterface $cache);

}