<?php
namespace Solarium\Cloud\Tests\Core\Zookeeper\ZkClusterState;

use PHPUnit\Framework\TestCase;
use Solarium\Cloud\Core\Zookeeper\ZkStateReader;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;


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

    public function testReadCollectionStates() {
        $collectionStates = $this->zkStateReader->getCollectionStates();
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
        $activeCollectionBaseUrls = $this->zkStateReader->getActiveCollectionBaseUrls('collection1');
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
