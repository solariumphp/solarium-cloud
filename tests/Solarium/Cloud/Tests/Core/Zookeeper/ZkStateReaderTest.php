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

namespace Solarium\Cloud\Tests\Core\Zookeeper\ZkClusterState;

use PHPUnit\Framework\TestCase;
use Solarium\Cloud\Core\Zookeeper\ZkStateReader;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Class ZkStateReaderTest
 * @package Solarium\Cloud\Tests\Core\Zookeeper\ZkClusterState
 */
class ZkStateReaderTest extends TestCase
{
    protected $zkStateReader;
    protected $cache;

    protected function setUp()
    {
        $this->cache = new FilesystemAdapter();
        $this->zkStateReader = new ZkStateReader('localhost:2181', $this->cache);
    }

    public function testReadCollectionAliases() {
        $collectionAliases = $this->zkStateReader->getCollectionAliases();
        print_r($collectionAliases);
        //$this->assertEquals('collection1', $configName);
    }

    public function testReadCollectionList() {
        $collectionList = $this->zkStateReader->getCollectionList();
        print_r($collectionList);
        //$this->assertEquals('collection1', $configName);
    }

    public function testReadCollectionState() {
        $collectionStates = $this->zkStateReader->getCollectionState('collection1');
        print_r($collectionStates);
        //$this->assertEquals('collection1', $configName);
    }

    public function testReadClusterStates() {
        $clusterStates = $this->zkStateReader->getClusterStates();
        print_r($clusterStates);
        //$this->assertEquals('collection1', $configName);
    }

    public function testReadClusterProperties() {
        $clusterProperties = $this->zkStateReader->getClusterProperties();
        print_r($clusterProperties);
        //$this->assertEquals('collection1', );
    }

    public function testReadLiveNodes() {
        $liveNodes = $this->zkStateReader->getLiveNodes();
        print_r($liveNodes);
        //$this->assertEquals('collection1', );
    }

    public function testGetCollectionName() {
        $configName = $this->zkStateReader->getCollectionName('collection1');
        $this->assertEquals('collection1', $configName);
        $configName = $this->zkStateReader->getCollectionName('collection');
        $this->assertEquals('collection1', $configName);
    }

    public function testActiveCollectionBaseUrls() {
        $activeCollectionBaseUrls = $this->zkStateReader->getActiveBaseUrls('collection1');
        //$this->assertEquals(, $activeCollectionEndpoints);
        print_r($activeCollectionBaseUrls);
    }

    public function testCollectionShardLeadersBaseUrl() {
        $collectionShardLeadersBaseUrl = $this->zkStateReader->getCollectionShardLeadersBaseUrl('collection1');
        //$this->assertEquals(, $activeCollectionEndpoints);
        print_r($collectionShardLeadersBaseUrl);
    }

    public function testCollectionEndpoints() {
        $endpoints = $this->zkStateReader->getCollectionEndpoints('collection1');
        print_r($endpoints);
    }

    public function testCollectionLeadersEndpoints() {
        $endpoints = $this->zkStateReader->getCollectionShardLeadersEndpoints('collection1');
        print_r($endpoints);
    }

    protected function tearDown()
    {
        //TODO close objects
    }
}
