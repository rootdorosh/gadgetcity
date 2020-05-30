<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Services\Crud;

use App\Modules\Product\Models\ProviderItem;

/**
 * Class ProviderItemCrudService
 */
class ProviderItemCrudService
{
    /*
     * @param    array $data
     * @return  ProviderItem
     */
    public function store(array $data): ProviderItem
    {
        $providerItem = ProviderItem::create($data);
        
        return $providerItem;
    }

    /*
     * @param    Product $providerItem
     * @param    ProviderItem $data
     * @return  ProviderItem
     */
    public function update(ProviderItem $providerItem, array $data): ProviderItem
    {
        $providerItem->update($data);
        
        return $providerItem;
    }

    /*
     * @param    ProviderItem $providerItem
     * @return  void
     */
    public function destroy(ProviderItem $providerItem): void
    {
        $providerItem->delete();
    }
    
    /*
     * @param      array $ids
     * @return    void
     */
    public function bulkDestroy(array $ids): void
    {
        ProviderItem::destroy($ids);
    }
    
    /*
     * @param      array $data
     * @return    void
     */
    public function bulkToggle(array $data): void
    {
        foreach (ProviderItem::whereIn('id', $data['ids'])->get() as $user) {
            $attr = $data['attribute'];
            $user->$attr = $data['value'];
            $user->save();
        }
    }
    
}
