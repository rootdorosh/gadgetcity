<?php 

declare( strict_types = 1 );

namespace App\Modules\Color\Services\Crud;

use App\Modules\Color\Models\Color;

/**
 * Class ColorCrudService
 */
class ColorCrudService
{
    /*
     * @param    array $data
     * @return  Color
     */
    public function store(array $data): Color
    {
        $color = Color::create($data);
        
        return $color;
    }

    /*
     * @param    Color $color
     * @param    Color $data
     * @return  Color
     */
    public function update(Color $color, array $data): Color
    {
        $color->update($data);
        
        return $color;
    }

    /*
     * @param    Color $color
     * @return  void
     */
    public function destroy(Color $color): void
    {
        $color->delete();
    }
    
    /*
     * @param      array $ids
     * @return    void
     */
    public function bulkDestroy(array $ids): void
    {
        Color::destroy($ids);
    }
    
    /*
     * @param      array $data
     * @return    void
     */
    public function bulkToggle(array $data): void
    {
        foreach (Color::whereIn('id', $data['ids'])->get() as $user) {
            $attr = $data['attribute'];
            $user->$attr = $data['value'];
            $user->save();
        }
    }
    
}
