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

namespace Solarium\Cloud\Tests\Core\Zookeeper;

use PHPUnit\Framework\TestCase;
use Solarium\Cloud\Core\Zookeeper\ZkStateReader;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Class ZkStateReaderTest
 * @package Solarium\Cloud\Tests\Core\Zookeeper
 */
class ZkStateReaderTest extends TestCase
{
    protected $cache;
    /* @var \Zookeeper */
    protected $zkClient;

    protected function setUp()
    {
        $this->zkClient = $this->getMockBuilder('\Zookeeper')
            ->setMethods(['exists', 'get', 'getChildren'])
            ->getMock();
        $this->zkClient->method('exists')->willReturn(true)->withConsecutive();
        $this->zkClient->method('getChildren')->withConsecutive(['/live_nodes'], ['/collections'], $this->any())
            ->willReturnOnConsecutiveCalls(
                ['localhost:8983_solr', 'localhost:8984_solr'],
                ['collection1'],
                ['shard1', 'shard2']
            );

        $this->zkClient->method('get')
            ->withConsecutive($this->any(), $this->any(), $this->any(), $this->any(), $this->any(), $this->any())
            ->willReturnOnConsecutiveCalls(
                ZookeeperTestData::ALIASES,
                '{}', // Legacy cluster.json
                ZookeeperTestData::COLLECTION_STATE,
                ZookeeperTestData::SHARD1_LEADER,
                ZookeeperTestData::SHARD2_LEADER,
                '{}' // security.json
            );
    }

    public function testReadClusterState() {
        $zkStateReader = new ZkStateReader($this->zkClient);
        $clusterState = $zkStateReader->getClusterState();

        $this->assertEquals(ZookeeperTestData::CLUSTER_STATE, $clusterState);
    }

    /**
     * Test if we can read the Collection State
     */
    public function testReadCollectionState() {
        $zkStateReader = new ZkStateReader($this->zkClient);
        $collectionState = $zkStateReader->getClusterState();
        $actual = base64_encode(serialize($collectionState));

        $this->assertEquals(ZookeeperTestData::COLLECTION_STATE_EXPECTED, $actual);
    }

    /**
     * Test if the cache component works
     */
    public function testCache()
    {
        $cache = new FilesystemAdapter();
        $zkStateReader = new ZkStateReader($this->zkClient, $cache);
        $clusterState = $zkStateReader->getClusterState();
        $zkStateReader2 = new ZkStateReader($this->zkClient, $cache);
        $clusterState2 = $zkStateReader2->getClusterState();

        $this->assertEquals($clusterState, $clusterState2);
    }
}