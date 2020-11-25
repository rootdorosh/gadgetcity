<?php 

declare( strict_types = 1 );

namespace App\Modules\Pattern\Services\Crud;

use App\Modules\Pattern\Models\Pattern;

/**
 * Class PatternCrudService
 */
class PatternCrudService
{
    /*
     * @param    array $data
     * @return  Pattern
     */
    public function store(array $data): Pattern
    {
        $pattern = Pattern::create($data);
        
        return $pattern;
    }

    /*
     * @param    Pattern $pattern
     * @param    Pattern $data
     * @return  Pattern
     */
    public function update(Pattern $pattern, array $data): Pattern
    {
        $pattern->update($data);
        
        return $pattern;
    }

    /*
     * @param    Pattern $pattern
     * @return  void
     */
    public function destroy(Pattern $pattern): void
    {
        $pattern->delete();
    }
    
    /*
     * @param      array $ids
     * @return    void
     */
    public function bulkDestroy(array $ids): void
    {
        Pattern::destroy($ids);
    }
    
    /*
     * @param      array $data
     * @return    void
     */
    public function bulkToggle(array $data): void
    {
        foreach (Pattern::whereIn('id', $data['ids'])->get() as $user) {
            $attr = $data['attribute'];
            $user->$attr = $data['value'];
            $user->save();
        }
    }
    
}
