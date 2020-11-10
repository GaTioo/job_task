<?php

namespace Tests\Unit;

use Tests\TestCase;

class Billy extends TestCase
{

	protected function setUp() : void {
		parent::setUp();
	}

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testBillyFieldsMethod()
    {

    	$this->setUp();

        $billy = new \App\Http\Controllers\Billy();

        $data = array(
            'type_id' => 'company',
            'name_test' => 'test',
            'country_id' => 'BG',
            'payment_terms_days' => 90
        );

        $expected = array(
        	'typeId' => 'company',
            'nameTest' => 'test',
            'countryId' => 'BG',
            'paymentTermsDays' => 90
        );

        $actual = $billy->billy_fields($data);

        $this->assertEquals($expected, $actual);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testBillySystemFieldsMethod()
    {

    	$this->setUp();

        $billy = new \App\Http\Controllers\Billy();

        $object = (object) array(
        	'id' => '123123',
        	'nameId' => '1234567890',
        	'type' => 'company',
        	'countryId' => 'BG',
        	'paymentTermsDays' => '90'
        );

        $expected = array(
            'name_id' => '1234567890',
            'type' => 'company',
            'country_id' => 'BG',
            'payment_terms_days' => 90,
            'external_id' => '123123'
        );

        $actual = $billy->billy_to_system_fields($object, 'insert'
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testBillyApiKey()
    {

    	$this->setUp();

    	$api = !empty(config('services.billy')['api']);

        $this->assertTrue($api, 'There must be api key in .env file - BILLY_API_KEY=API_KEY');
    }
}
