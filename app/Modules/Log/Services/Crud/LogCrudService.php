<?php

namespace App\Modules\Log\Services\Crud;

use App\Modules\Event\Models\Log;

/**
 * Class LogCrudService
 */
class LogCrudService
{
    /*
     * @param   Log $log
     * @return  void
     */
    public function destroy(Log $log): void
    {
        $log->delete();
    }
    
}
