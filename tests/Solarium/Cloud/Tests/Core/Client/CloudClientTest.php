<?php
namespace Solarium\Cloud\Tests\Core\Client;

use PHPUnit\Framework\TestCase;

class CloudClientTest extends TestCase { 
    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $options = array('zkhosts' => 'localhost:2181');
        $this->client = new \Solarium\Cloud\Client($options);
    }

    public function testSolrCloud()
    {
        $this->client->setCollection('collection1');
        $query = $this->client->createSelect();
        $result = $this->client->select($query);
        print_r($result);
    }
    
}
