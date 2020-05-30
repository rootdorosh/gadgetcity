<?php

namespace App\Modules\User\Services\Crud;

use App\Modules\User\Models\User;
use App\Services\Image\ImageService;


/**
 * Class UserCrudService
 */
class UserCrudService
{
    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * UserCrudService constructor.
     *
     * @param ImageManagerInterface $imageService
     */
    public function __construct(ImageService $imageService) 
    {
        $this->imageService = $imageService;
    }

    /*
     * @param   array $data
     * @return  User
     */
    public function store(array $data): User
    {
        $data = $this->saveMedia($data);
        $user = User::create($data);
        $user->roles()->sync(!empty($data['roles']) ? $data['roles'] : []);
        
        return $user;
    }

    /*
     * @param   User $user
     * @param   User $data
     * @return  User
     */
    public function update(User $user, array $data): User
    {
        $data = $this->saveMedia($data);
        $user->update($data);
        $user->roles()->sync(!empty($data['roles']) ? $data['roles'] : []);
        
        return $user;
    }

    /*
     * @param   User $user
     * @return  void
     */
    public function destroy(User $user): void
    {
        $user->delete();
    }
    
    /**
     *  Save product files.
     *
     * @param array $data
     * @return array
     */
    private function saveMedia(array $data) : array
    {
        if (!empty($data['image_file'])) {
            $data['image'] = $this->imageService->upload($data['image_file']);
        }

        return $data;
    }
    
    /*
     * @param   array $ids
     * @return  void
     */
    public function bulkDestroy(array $ids): void
    {
        User::destroy($ids);
    }
    
    /*
     * @param   array $data
     * @return  void
     */
    public function bulkToggle(array $data): void
    {
        foreach (User::whereIn('id', $data['ids'])->get() as $user) {
            $attr = $data['attribute'];
            $user->$attr = $data['value'];
            $user->save();
        }
    }
    
}
