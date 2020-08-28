<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ProductsTest extends TestCase
{   
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        // seed the database
        $this->artisan('db:seed');
    }

    /**
     * Test to check if the endpoint can get all products
     *
     * @return void
     */
    public function testItCanGetAllProducts()
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200);
    }

    /**
     * Test to check if the endpoint can get a single product resource
     * 
     * @return void
     */
    public function testItCanGetSingleProductResource()
    {   
        // Add a product into database
        $data = $this->getTestData();
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])
        ->json('POST', '/api/products', $data)
        ->decodeResponseJson();
            
        $response = $this->get('/api/products/'.$response["data"]["product_id"]);

        $response->assertStatus(200)->assertJson([
            'data' => [
                'product_name' => $data['product_name'],
                'product_description' => $data['product_description'],
                'product_price' => $data['product_price']
            ]
        ]);
    }

    /**
     * Test to check if the endpoint can create a product
     * 
     * @return void
     */
    public function testItcanCreateANewProduct()
    {   
        $data = $this->getTestData();
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])
        ->json('POST', '/api/products', $data);

        $response->assertStatus(201)->assertJson([
            'data' => [
                'product_name' => $data['product_name'],
                'product_description' => $data['product_description'],
                'product_price' => $data['product_price']
            ]
        ]);
    }

    /**
     * Test to check if the endpoint can update a product
     * 
     * @return void
     */
    public function testItcanUpdateAProduct()
    {   
        $product_id = $this->createSingleProduct();

        $data = $this->getTestData();
            
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])
        ->json('PATCH', '/api/products/'.$product_id, $data);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'product_name',
                'product_description',
                'product_price',
                'product_id',
                'category_name',
                'category_id'
            ]
        ]);
    }

    /**
     * Test to check if the endpoint can delete a product
     * 
     * @return void
     */
    public function testItCanDeleteAProduct()
    {   
        $product_id = $this->createSingleProduct();

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])
        ->json('DELETE', '/api/products/'.$product_id, []);

        $response->assertStatus(204);
    }

    /**
     * Test to check if the endpoint can return 404 on invalid product
     * 
     * @return void
     */
    public function testItCanReturn404OnInvalidProduct()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])
        ->get('/api/products/33333333');

        $response->assertStatus(404);
        $response->assertExactJson([
            'errors' => 'Resource not found!',
        ]);
    }

    /**
     * Test to check if the endpoint can return 404 on trying to update an invalid product
     * 
     * @return void
     */
    public function testItThrowsErrorWhenUpdatingInvalidProduct()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])
        ->json('PATCH', '/api/products/222222', $this->getTestData());

        $response->assertStatus(404);
    }

    /**
     * Test to check if the endpoint handle a bad request
     * 
     * @return void
     */
    public function testItReturnsBadRequestOnMissingDataWhenCreatingProduct()
    {   
        $data =  $this->getTestData();

        // Unset description. All fields are required when saving a product
        unset($data["product_description"]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])
        ->json('POST', '/api/products', $data);

        $response->assertStatus(400);
    }

    /**
     * Test to check if the endpoint can create a product in a different language
     * 
     * @return void
     */
    public function testItCanCreateAProductInDifferentLanguage()
    {
        $data = $this->getTestData();
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-localization' => 'fr-ch'
        ])
        ->json('POST', '/api/products', $data);

        $response->assertStatus(201)->assertJson([
            'data' => [
                'product_name' => $data['product_name'],
                'product_description' => $data['product_description'],
                'product_price' => $data['product_price']
            ]
        ]);
        
        // Translation available
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-localization' => 'fr-ch'
        ])->get('/api/products/'.$response["data"]["product_id"]);

        $response->assertStatus(200)->assertJson([
            'data' => [
                'product_name' => $data['product_name'],
                'product_description' => $data['product_description'],
                'product_price' => $data['product_price']
            ]
        ]);

        // Translation not available
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-localization' => 'en-gb'
        ])->get('/api/products/'.$response["data"]["product_id"]);

        $response->assertStatus(404);
        $response->assertExactJson([
            'errors' => 'Resource not found!',
        ]);
    }

    /**
     * Function to create a single test product
     * 
     * @return int product_id
     */
    private function createSingleProduct()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])
        ->json('POST', '/api/products', $this->getTestData())
        ->decodeResponseJson();

        $this->clearCache();

        return $response["data"]["product_id"];
    }

    /**
     * Creates test data array
     * 
     * @return array $data
     */
    private function getTestData()
    {
        $data = [
            'product_name' => 'Test Product',
            'product_description' => "Test description",
            'product_price' => '22',
            'category_id' => 1
        ];

        return $data;
    }

    /**
     * Clears Laravel Cache
     * 
     */
    private function clearCache()
    {
        $commands = ['clear-compiled', 'cache:clear', 'view:clear', 'config:clear', 'route:clear'];
        foreach ($commands as $command) {
            \Illuminate\Support\Facades\Artisan::call($command);
        }
    }
}
