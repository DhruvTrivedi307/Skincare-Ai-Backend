<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\SkinConcerns;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function products()
    {
        $products = Products::with('concerns')->paginate(10);
        $concerns = SkinConcerns::all();
        return view("products", compact("products", "concerns"));
    }

    public function add_product(Request $request)
    {
        $request->validate([
            "product_name" => "required|string|max:255",
            "product_image" => "nullable|image|max:5120"
        ]);

        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
        }

        $product = Products::create([
            "name" => $request->product_name,
            "image" => isset($imageName) ? $imageName : null,
        ]);

        if ($request->has('skin_concern_id')) {
            $product->concerns()->attach($request->skin_concern_id);
        }

        return redirect()->route('products')->with('success', 'Product added successfully.');
    }

    public function update_product(Request $request, $id)
    {
        $request->validate([
            "product_name" => "required|string|max:255",
            "product_image" => "nullable|image|max:5120"
        ]);

        $product = Products::findOrFail($id);
        $product->name = $request->product_name;

        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        if ($request->has('skin_concern_id')) {
            $product->concerns()->sync($request->skin_concern_id);
        } else {
            $product->concerns()->detach();
        }

        return redirect()->route('products')->with('success', 'Product updated successfully.');
    }

    public function delete_product($id)
    {
        $product = Products::findOrFail($id);
        $product->delete();

        return redirect()->route('products')->with('success', 'Product deleted successfully.');
    }

    public function concern_add(Request $request){
        $request->validate([
            "concern_name" => "required|string"
        ]);

        SkinConcerns::create([
            'concern' => $request->concern_name
        ]);

        return redirect()->route('products')->with('success', 'Concern added successfully');
    }

    public function concern_delete($id){
        SkinConcerns::find($id)->delete();
        return redirect()->route('products')->with('success', 'Concern deleted successfully');
    }
}
