<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Services\Crud;

use App\Modules\Product\Models\Product;

/**
 * Class ProductCrudService
 */
class ProductCrudService
{
    /*
     * @param    array $data
     * @return  Product
     */
    public function store(array $data): Product
    {
        $product = Product::create($data);
        
        return $product;
    }

    /*
     * @param    Product $product
     * @param    Product $data
     * @return  Product
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        
        return $product;
    }

    /*
     * @param    Product $product
     * @return  void
     */
    public function destroy(Product $product): void
    {
        $product->delete();
    }
    
    /*
     * @param      array $ids
     * @return    void
     */
    public function bulkDestroy(array $ids): void
    {
        Product::destroy($ids);
    }
    
    /*
     * @param      array $data
     * @return    void
     */
    public function bulkToggle(array $data): void
    {
        foreach (Product::whereIn('id', $data['ids'])->get() as $user) {
            $attr = $data['attribute'];
            $user->$attr = $data['value'];
            $user->save();
        }
    }
    
}
