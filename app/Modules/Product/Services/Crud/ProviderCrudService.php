<?php 

declare( strict_types = 1 );

namespace App\Modules\Product\Services\Crud;

use App\Modules\Product\Models\Provider;

/**
 * Class ProviderCrudService
 */
class ProviderCrudService
{
    /*
     * @param    array $data
     * @return  Provider
     */
    public function store(array $data): Provider
    {
        $provider = Provider::create($data);
        
        return $provider;
    }

    /*
     * @param    Product $provider
     * @param    Provider $data
     * @return  Provider
     */
    public function update(Provider $provider, array $data): Provider
    {
        $provider->update($data);
        
        return $provider;
    }

    /*
     * @param    Provider $provider
     * @return  void
     */
    public function destroy(Provider $provider): void
    {
        $provider->delete();
    }
    
    /*
     * @param      array $ids
     * @return    void
     */
    public function bulkDestroy(array $ids): void
    {
        Provider::destroy($ids);
    }
    
    /*
     * @param      array $data
     * @return    void
     */
    public function bulkToggle(array $data): void
    {
        foreach (Provider::whereIn('id', $data['ids'])->get() as $user) {
            $attr = $data['attribute'];
            $user->$attr = $data['value'];
            $user->save();
        }
    }
    
}
