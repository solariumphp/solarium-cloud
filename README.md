# solarium-cloud
solarium extension to connect to SolrCloud via Zookeeper

This is an alpha version of solarium-cloud. Do not use in production yet.

You can create a CloudClient using the following code:

    <?php
    
    $options = array('zkhosts' => 'localhost:2181');
        $this->client = new CloudClient($options);
    }

    public function testSolrCloud()
    {
        $this->client->setCollection('collection1');
        $query = $this->client->createSelect();
        $result = $this->client->select($query);
        print_r($result);
