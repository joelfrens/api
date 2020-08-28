<?php

namespace App\Http\Controllers;

use App\model\Product;
use App\model\Language;
use App\model\ProductTranslations;
use Illuminate\Http\Request;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Requests\ProductRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $locale = App::getLocale();
        $products = DB::table('products')
                    ->join('product_translations', 'products.id', '=', 'product_translations.product_id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->join('languages', 'product_translations.language_id', '=', 'languages.id')
                    ->select('products.product_price', 'products.category_id', 'product_translations.product_name', 
                    'product_translations.product_description', 'categories.category_name', 'categories.id')
                    ->where('languages.language_code', $locale)
                    ->get();
        return ProductCollection::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $locale = App::getLocale();

        // Check POST data
        $checkRequest = $this->checkRequest($request);

        $errors = $checkRequest['errors'];

        // If some inputs are missing send a bad request response
        if ($errors) {
            return response()->json(
				[
					'data' => $checkRequest
				],
				Response::HTTP_BAD_REQUEST
			);
        }

        $productData = [
            'product_price' => $request->product_price,
            'category_id' => 1
        ];

        $productId = Product::create($productData)->id;

        //get Language id
        $language = Language::where('language_code', $locale)->first();

        $productTranslationData = [
            'product_name' => $request->product_name,
            'product_description' => $request->product_description,
            'product_id' => $productId,
            'language_id' => $language->id
        ];

        // Add data to the translation table
        $productTranslation = ProductTranslations::create($productTranslationData);

        // Send the data back as response
        $product = $this->getProductById($productId, $locale);

        return response(
            [
                'data' => new ProductResource($product)
            ], 
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\model\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {   
        $locale = App::getLocale();

        // Check if a product exists with Translation
        $id = $product->id;
        $isProductInDb = $this->getProductById($id, $locale);

        if (is_null($isProductInDb)) {
            return response()->json(
				[
					'errors' => 'Resource not found!'
				],
				Response::HTTP_NOT_FOUND
			);
        }

        $productData = $this->getProductById($id, $locale);

        return new ProductResource($productData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\model\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {   
        $locale = App::getLocale();

        // Check if a product exists with Translation
        $id = $product->id;
        $isProductInDb = $this->getProductById($id, $locale);

        if (is_null($isProductInDb)) {
            return response()->json(
				[
					'errors' => 'Resource not found!'
				],
				Response::HTTP_NOT_FOUND
			);
        }

        $oldProduct = Product::find($id);

        if (isset($request->product_price)) {
            $oldProduct->product_price = $request->product_price;
        }
        $oldProduct->category_id = 1;
        $oldProduct->save();

        //get Language id
        $language = Language::where('language_code', $locale)->first();

        $data = [];
        if (isset($request->product_name)) {
            $data['product_name'] = $request->product_name;
        }
        if (isset($request->product_description)) {
            $data['product_description'] = $request->product_description;
        }

        ProductTranslations::where('product_id', $oldProduct->id)
            ->where('language_id', $language->id)
            ->update($data);

        // Send the data back as response
        $product = $this->getProductById($oldProduct->id, $locale);

        return response(
            [
                'data' => new ProductResource($product)
            ], 
            Response::HTTP_CREATED
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\model\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Fetches a single product by Id.
     *
     * @param  int  $id
     * @param  string  $locale
     * @return \App\model\Product
     */
    private function getProductById($id, $locale)
    {
        $productData =  DB::table('products')
                        ->join('product_translations', 'products.id', '=', 'product_translations.product_id')
                        ->join('categories', 'products.category_id', '=', 'categories.id')
                        ->join('languages', 'product_translations.language_id', '=', 'languages.id')
                        ->select('products.id as product_id', 'products.product_price', 'products.category_id', 
                        'product_translations.product_name', 'product_translations.product_description', 
                        'categories.category_name', 'categories.id')
                        ->where('languages.language_code', $locale)
                        ->where('products.id', $id)
                        ->first();

        return $productData;
    }

    /**
     * Checks for a valid POST/PATCH request
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return Array $result
     */
    private function checkRequest($request)
    {   
        $errors = false;
        $message = [];
        $result = [];
        if (!isset($request->product_price) || trim($request->product_price) == '') {
            $errors = true;
            $message[] = 'Please provide the product price';
        }
        if (!isset($request->product_name) || trim($request->product_name) == '') {
            $errors = true;
            $message[] = 'Please provide the product name';
        }
        if (!isset($request->product_description) || trim($request->product_description) == '') {
            $errors = true;
            $message[] = 'Please provide the product description';
        }

        $result["errors"] = $errors;
        $result["message"] = $message;

        return $result;
    }
}
