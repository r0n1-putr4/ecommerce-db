<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;


class CartController extends Controller
{

    private $client;

    public function __construct()
    {

        $appEnv = env('APP_ENV', 'local');
        $baseUri = $appEnv == 'local' ? 'http://localhost:3000' : '';
        $this->client = new \GuzzleHttp\Client(['base_uri' => $baseUri]);
    }

    public function getProduct($productId = null)
    {
        try {
            $url = $productId ? "/products/{$productId}" : '/products';
            $response = $this->client->request('GET', $url);
            $responseData = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() === 200 && isset($responseData['data'])) {
                return $responseData['data'];
            }
            return null;
        } catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return null;
        }
    }


    public function index()
    {
        try {
            $cartItems = Cart::all();
            return ResponseHelper::successResponse('Cart List', $cartItems);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error([
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return ResponseHelper::errorResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $cartItem = Cart::find($id);

            if (!$cartItem) {
                return ResponseHelper::errorResponse('Cart Item Not Found');
            }

            return ResponseHelper::successResponse('Cart Item', $cartItem);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error([
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return ResponseHelper::errorResponse($th->getMessage());
        }
    }
    public function store(Request $request)
    {

        $validate = $this->validate($request, [
            'product_id'    => 'required|integer',
            'quantity'      => 'required|integer',
        ]);

        try {

            $product = $this->getProduct($validate['product_id']);
          

            if (!$product) return ResponseHelper::errorResponse('Product Not Found', 404);

            $cartItem = Cart::create([
                'product_id'    => $validate['product_id'],
                'name'          => $product['name'],
                'quantity'      => $validate['quantity'],
                'price'         => $product['price'] * $validate['quantity']
            ]);

            if (!$cartItem) return ResponseHelper::errorResponse('Failed to create cart item', 500);

            return ResponseHelper::successResponse('Cart item created successfully', $cartItem);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error([
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return ResponseHelper::errorResponse($th->getMessage());
        }
    }
}
