<?php 

declare( strict_types = 1 );

namespace App\Base\Eloquent;

use Illuminate\Database\Eloquent\Model;

class BaseModelLang extends Model
{
    
    /**
     * primary key.
     *
     * @var  string
     */
    protected $primaryKey = 'translation_id';
     
    /**
     * Owner field name.
     *
     * @var string
     */
    public $ownerField;
    
    
    /**
     * @var  bool
     */
    public $timestamps = false;
    
    /**
     * Scope a query to only include popular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTranslation($query)
    {
        $mainTable = str_replace('_lang', '', $this->table);
        
        return $query->where($this->table . '.locale', l())
            ->where($this->table . '.is_translated', 1)
            ->leftJoin($mainTable, $mainTable . '.id', '=', $this->table . '.' . $this->ownerField);
    }    
    
}
