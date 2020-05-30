<?php
declare( strict_types = 1 );

namespace App\Services\Image;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use App\Services\Storage\ImageStorageManager;
use Illuminate\Database\Eloquent\Model;
use App\Services\Curl;


/**
 * Class ImageService
 * @package App\Services\Image
 */
class ImageService implements ImageManagerInterface
{
    /**
     * @var ConvertToWebp
     */
    private $convertToWebp;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var ImageStorageManager
     */
    private $storageManager;
    
    /**
     * @var Repository
     */
    private $configRepository;

    /**
     * ImageService constructor.
     * @param ImageManager        $imageManager
     * @param ImageStorageManager $storageManager
     * @param Repository          $repository
     * @param ConvertToWebp       $convertToWebp
     */
    public function __construct(
        ImageManager $imageManager,
        ImageStorageManager $storageManager,
        Repository $repository,
        ConvertToWebp $convertToWebp
    ) {
        $this->imageManager     = $imageManager;
        $this->storageManager   = $storageManager;
        $this->configRepository = $repository;
        $this->convertToWebp = $convertToWebp;
    }

    /**
     * @param mixed  $data
     * @param string|null $imageName
     * @return string
     */
    public function save($data, string $imageName = null) : string
    {
        $image = $this->prepareImage($data);

        if (!$imageName) {
            $imageName = md5_file($data);
        }
        
        $filePath = $this->storageManager->save($image->stream()->getContents(), $imageName);

        return $filePath;
    }

    /**
     * @param string $base64Content
     * @param array $options
     * @return string
     */
    public function saveFromBase64(string $base64Content, array $options = []) : string
    {
        return $this->saveFromBinary(
            $this->getFileContentFromBase64($base64Content), 
            $options
        );
    }
    
   /**
     * @param string $url
     * @param string $name
     * @return string
     */
    public function uploadByUrl(string $url, string $name = null) : string
    {
        if (!$name) {
            $ext = Str::before(Str::afterLast($url, '.'), '?');
            $name = md5(time().$url) . '.' . $ext;
        }
         
        return $this->saveFromBinary(
            Curl::getPage($url, ['cache' => 'false']),
            ['name' => $name]
        );
    }
    
    
    /**
     * @param array $data
     * @param array $oldData
     * @return string
     */
    public function saveAdaptiveImageBase64(array $data, array $oldData = []) : array
    {
        $data = array_to_tree_by_keys($data, '->');
        
        $rand = Str::random(10);
        
        $actualImages = [];
        
        foreach ($data as $size => $params) {
            if (!empty($params['image']) && is_array($params['image'])) {
                $content = $this->getFileContentFromBase64($params['image']['content']);
                
                $data[$size]['image'] = $this->saveFromBinary(
                    $content,
                    ['name' => $params['image']['name'], 'withBasePath' => true]
                );
            }
            $actualImages[] = $data[$size]['image'];
        }
        
        foreach ($oldData as $item) {
            if (!in_array($item['image'], $actualImages)) {
                if (!empty($item['image'])) {
                    $this->removeOldImage($item['image']);
                }
            }
        }
        
        return $data;
    }
    
    /*
     * @param string $file
     * @return void
     */
    public function removeOldImage(string $file)
    {
        // TODO ... removo image and crop resize thumbs
    }
    
    /**
     * @param string $binary
     * @param string $imageName
     * @param array $options
     * @return string
     */
    public function saveFromBinary(string $binary, array $options = []) : string
    {
        $imageName = !empty($options['name']) ? 
            $this->normalizeFilename($options['name']) : md5($binary) . '.png';
        
        $filePath = ImageStorageManager::UPLOAD_PATH . $this->storageManager->save(
            $binary, 
            $imageName,
            $options
        );
        
        
        $mimeType = mime_content_type(public_path() . $filePath);
        
        $file = !empty($options['withBasePath']) 
            ? $filePath 
            : str_replace(ImageStorageManager::UPLOAD_PATH, '', $filePath);
        
        
        return $file;
    }
    
    /**
     * @param string $filename
     * @return string
     */
    public function normalizeFilename(string $filename) : string
    {
        $expl = explode('.', $filename);
        $ext = array_pop($expl);
        
        return Str::slug(implode('', $expl)) . '.' . $ext;
    }

   /*
     * @param string $binary
     * @return string
     */
    public function getExtBinary(string $binary): string
    {
        preg_match('/data:image\/(.*?)\;base64/', $binary, $match);
        
        return !empty($match[1]) ? $match[1] : 'jpg';
    }
    
    /**
     * @param string $base64Content
     * @return string
     */
    public function getFileContentFromBase64(string $base64Content) : string
    {
        $expl = explode("base64,", $base64Content);
        
        return base64_decode($expl[1]);
    }
    
    
    /**
     * @param mixed $data
     * @return Image
     */
    private function prepareImage($data) : Image
    {
        $image = $this->imageManager->make($data);

        $resizeData = $this->configRepository->get('image.resize');

        //$image->resize($resizeData[ 'width' ], $resizeData[ 'height' ]);

        return $image;
    }

    /**
     * @param UploadedFile $imageFile
     * @param array $params
     * @return string
     */
    public function upload(UploadedFile $imageFile, array $params = []) : string
    {
        $name = strtolower(Str::random(8));
        
        $fileName = $name . '.' . $imageFile->extension();
        
        return $this->save($imageFile, $fileName);
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function deleteOld(string $path)
    {
        //info($path);
    }

    /**
     * @param string $attribute
     * @param array $data
     * @param Model|null $model
     * @return mixed
     */
    public function attachImage(string $attribute, array $data, Model $model = null): array
    {
        // image data must be array type
        if (!empty($data[$attribute]) && !is_array($data[$attribute])) {
            unset($data[$attribute]);
            return $data;
        }
        
        $oldImage = ($model && $model->$attribute) ? $model->$attribute : null;
        $doRemoveImage = null;

        if (!empty($data[$attribute]) && is_array($data[$attribute])) {
            $oldImage = $model->$attribute ?? null;
            $data[$attribute] = $this->saveFromBase64($data[$attribute]['content'], [
                'name' => $data[$attribute]['name'],
                'oldImage' => $oldImage,
            ]);
            $doRemoveImage = $oldImage;
        } elseif (empty($data[$attribute])) {
            $doRemoveImage = $oldImage;
            $data[$attribute] = null;
        } 

        if (!empty($doRemoveImage)) {
            $this->deleteOld($doRemoveImage);
        }
        return $data;
    }
}
