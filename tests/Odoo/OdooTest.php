<?php


use Edujugon\Laradoo\Odoo;
use PHPUnit\Framework\TestCase;

class OdooTest extends TestCase
{


    protected $odoo;

    /**
     * Demo credentials set.
     */
    protected $host;
    protected $db;
    protected $username;
    protected $password;

    public function __construct()
    {
        parent::__construct();

        $this->odoo = new Odoo();

        $this->setDemoCredentials();
        $this->createOdooInstance();
    }

    /**
     * Set odoo.com test credentials
     */
    protected function setDemoCredentials()
    {

        $info = $this->odoo->getRipcord()::client('https://demo.odoo.com/start')->start();

        list($this->host, $this->db, $this->username, $this->password) =
            array($info['host'], $info['database'], $info['user'], $info['password']);

    }

    /**
     * Connect with the odoo and create the oddo instance.
     */
    protected function createOdooInstance()
    {
        //dd('url: ' . $this->host . ' db:' . $this->db . ' user:' . $this->username . ' pass:' . $this->password);
        $this->odoo = $this->odoo
            ->username($this->username)
            ->password($this->password)
            ->db($this->db)
            ->host($this->host)
            ->connect();
    }


    /** @test */
    public function get_odoo_version()
    {
        $version = $this->odoo->version();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $version);
    }


    /** @test */
    public function test_common_connection_odoo()
    {
        $this->assertInternalType('integer', $this->odoo->getUid());

    }


    /** @test */
    public function check_access_to_models()
    {
        $check = $this->odoo->can('read', 'res.partner');

        $this->assertEquals(1,$check);
    }

    /** @test */
    public function using_search_method()
    {
        $ids = $this->odoo
            ->where('customer', '=', true)
            ->search('res.partner');

        $this->assertArrayNotHasKey('faultCode',$ids);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $ids);
        $this->assertNotEmpty($ids);
    }

    /** @test */
    public function count_items()
    {
        $amount = $this->odoo->count('res.partner');

        $this->assertInternalType('integer', $amount);
    }

    /** @test */
    public function get_limited_ids()
    {
        $ids = $this->odoo
            ->where('customer', '=', true)
            ->limit(3)
            ->search('res.partner');

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $ids);
        $this->assertArrayNotHasKey('faultCode',$ids);
        $this->assertCount(3, $ids);
    }

    /** @test */
    public function retrieve_a_model_with_few_fields()
    {
        $models = $this->odoo
            ->where('customer', true)
            ->limit(3)
            ->fields('name')
            ->get('res.partner');

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $models);
        $this->assertArrayNotHasKey('faultCode',$models);
        $this->assertCount(3, $models);

    }

    /** @test */
    public function get_fields_of_partner_model()
    {
        $fields = $this->odoo->fieldsOf('res.partner');

        $this->assertInstanceOf(\Illuminate\Support\Collection::class,$fields);
        $this->assertArrayNotHasKey('faultCode',$fields);
    }

    /** @test */
    public function create_new_record()
    {
        $id = $this->odoo
            ->create('res.partner',['name' => 'Jonh Odoo']);

        $this->assertInternalType('integer',$id);
    }

    /** @test */
    public function delete_a_record()
    {
        $this->odoo
            ->create('res.partner',['name' => 'Jonh Odoo']);
        $this->odoo
            ->create('res.partner',['name' => 'Jonh Odoo']);
        $id = $this->odoo
            ->where('name', 'Jonh Doe Odoo')
            ->search('res.partner');


        $result = $this->odoo->deleteById('res.partner',$id->get(0));

        $this->assertInternalType('boolean',$result);
    }

    /** @test */
    public function delete_a_record_directly()
    {
        // Create a record
        $this->odoo->create('res.partner',['name' => 'Jonh Odoo']);

        // Delete it
        $result = $this->odoo->where('name', 'Jonh Odoo')
            ->delete('res.partner');

        $this->assertInternalType('boolean',$result);
    }

}