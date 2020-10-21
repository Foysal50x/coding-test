<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\QueryFilters\ProductFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ProductFilter $filter
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(ProductFilter $filter)
    {
        return view('products.index')->with([
            "products" => Product::with('priceVariants')->filter($filter)->orderByDesc('created_at')->paginate(5),
            "variants" => Variant::with(['variants' => function($query){
                return $query->groupBy('variant');
            }])->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }


    public function store(StoreProductRequest $request)
    {
        try {
            DB::transaction(function() use($request){
                /** @var Product $product */
                $product = Product::create($request->all());
                $product->images()->createMany(array_map(function($image){
                    return [
                        "file_path" => $image
                    ];
                }, $request->input("product_image")));
                collect($request->input('product_variant'))->each(function($variant) use($product){
                    $variant_id = $variant['option'];
                    $variant_array = array_map(function($name) use ($variant_id) {
                        return ["variant" => $name, "variant_id" => $variant_id];
                    }, $variant["tags"]);
                    $product->productVariants()->createMany($variant_array);

                });
                $id = $product->id;
                collect($request->input('product_variant_prices'))->each(function($productVariant) use($id){
                    $array = array_filter(explode('/', $productVariant['title']));
                    $product_variant_array = [
                        "price" => $productVariant["price"],
                        "stock" => $productVariant["stock"],
                        "product_id" => $id
                    ];
                    $match = ["one", "two", "three"];
                    foreach($array as $key => $variant) {
                        $pv = ProductVariant::query()->where([
                            ['product_id', '=', $id],
                            ['variant', 'LIKE', $variant]
                        ])->first();
                        if ($pv) {
                            $product_variant_array["product_variant_{$match[$key]}"] = $pv->id;
                        }
                    }
                    ProductVariantPrice::create($product_variant_array);
                });
            });
            return Response::json(["success" => true, "message" => "Product create success"]);
        }catch (\Exception $exception){
            info($exception->getMessage());
            Response::json(["success" => false, "message" => $exception->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    public function edit(Product $product)
    {
        $product->load(['priceVariants', 'variants' => function($query) use ($product){
            $query->with(['variants' => function($query) use($product){
                $query->whereProductId($product->id)->groupBy('variant');
            }])->groupBy('title');
        }]);
        //dd($product->toArray());
        $variants = Variant::all();
        return view('products.edit')->with([
            "variants" => $variants,
            "product"  => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
