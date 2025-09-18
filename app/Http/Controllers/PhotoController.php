<?php

namespace App\Http\Controllers;

use App\Models\Photo;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Decoders\FilePathImageDecoder;
use Intervention\Image\Typography\FontFactory;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Support\Str; // Import Str facade
use Illuminate\Support\Facades\Config;
use Illuminate\Pagination\Paginator;

use App\Traits\RazdelTrait;

class PhotoController extends Controller
{
    use RazdelTrait;

    private ImageManager $imageManager;
    private string $dashboard = 'photos';
    public string $dashboard_title = 'Обработка фотографий сайта';
    private string $err = '';
    private string $imagesDirectory = 'images';
    private string $thumbnailsDirectory = 'thumbnails';
    private int $perPage = 6;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
        if (!Storage::disk('public')->exists($this->imagesDirectory)) {
            Storage::makeDirectory($this->imagesDirectory);
        }
        if (!Storage::disk('public')->exists($this->thumbnailsDirectory)) {
            Storage::makeDirectory($this->thumbnailsDirectory);
        }
    }

    private function getImageName($fileName = ''){
        if (empty($fileName)){
            return '';
        }
        $filePath = explode('/', $fileName);
        return end($filePath);
    }

    public static function getPhotoGallery($razdel_id)
    {
        return Photo::where('razdel_id', '=', $razdel_id)
            ->get()
            ->map(function($image) {
                return [
                    'id' => $image->id,
                    'razdel_id' => $image->razdel_id,
                    'title' => $image->title,
                    'filename' => '/storage/'.$image->filename,
                    'thumbnail' => '/storage/'.$image->thumbnail,
                    'description' => $image->description,
                ];
        });
    }

    private function getPhoto($params = [])
    {
        if (!empty($params['id'])){
            $photos = Photo::where('id','=', $id)
            ->get();
        } else {
            $photos = Photo::orderby('id', 'desc')
            ->where('razdel_id', '=', $params['razdel_id'])
            ->get();
        }
        return $photos->map(function($photo){
            return [
                'id' => $photo->id,
                'razdel_id' => $photo->razdel_id,
                'title' => $photo->title,
                'url' => $photo->getUrl(),
                'thumbnail' => $photo->getThumbnailUrl(),
                'description' => $photo->description,
            ];
        });
    }

    public function show($razdel_id)
    {
        $razdel_id = (!isset($razdel_id) || empty($razdel_id) || !$razdel_id) ? 1 : $razdel_id;
        $params = [
            'dashboard' => $this->dashboard,
            'dashboard_title' => $this->dashboard_title,
            'razdel_id' => $razdel_id,
            'razdels' => $this->getRazdelsList($razdel_id),
            'photos' => $this->getPhoto([
                'razdel_id' => $razdel_id
            ]),
            'err' => $this->err,
        ];
        return Inertia::render('Dashboard', $params);
    }

    public function index(Request $request)
    {
        return $this->show($request->razdel_id);
    }

    public function update(Request $request)
    {
        foreach ($request->photos as $key => $val){
            $razdel_id = $val['razdel_id'];
            if (empty($val['del'])){
                Photo::where('id', '=', $val['id'])
                ->update([
                    'title' => $val['title'],
                    'description' => $val['description'],
                ]);
            } else {
                Photo::destroy($val['id']);
            }
        }
        return $this->show($razdel_id);
    }

    private function getImageParams($path)
    {
        $image = $this->imageManager->read($path);
        $size = $image->size();
        $resolution = $image->resolution();
        return [
            'width' => $image->width(),
            'height' => $image->height(),
            'ratio' => $size->aspectRatio(),
            'is_portrait' => $size->isPortrait(),
            'is_landscape' => $size->isLandscape(),
            'resolution_x' => $resolution->x(),
            'resolution_Y' => $resolution->Y(),
        ];
    }

    // Наложение водяного знака
    protected function createWaterMark($path, $size, $text, $type)
    {
        $imageParams = $this->getImageParams($path);
        $pos_x = $imageParams['width'] / 2;
        $pos_y = $imageParams['height'] - 100;
        try{
            $img = $this->imageManager->gd()->read($path);
            $img->text($text, $pos_x, $pos_y, function (FontFactory $font) {
                $font->file(public_path('fonts/CyrillicOld.ttf'));
                $font->size(45);
                $font->color('fff');
                $font->stroke('ff5500', 2);
                $font->align('center');
                $font->valign('middle');
                $font->lineHeight(1.6);
                $font->wrap(250);
            })->save();
        } catch (Throwable $e) {
            report($e);
        }
    }

    // Изменяем размер изображения
    protected function createResizedImage($path, $size, $type)
    {
        $filePath = explode('/', $path);
        $imageName = end($filePath);
        $imagePath = public_path('storage/' .$this->imagesDirectory. '/'. $imageName);
        $img = $this->imageManager->read($imagePath);
        $img->scale($size, $size);
        if ($type === 'images'){
            $thumbnailPath = $imagePath;
            $img->save();
        } else {
            $thumbnailPath = public_path('storage/' .$this->thumbnailsDirectory. '/'. $imageName);
            $img->save($thumbnailPath);
        }
        return $thumbnailPath;
    }

    public function upload(Request $request)
    {
        $images_params = config('custom.image_params');
        $watermark_text = $images_params['watermark_text'];
        $size_images = $images_params['size_images'];
        $size_thumbnails = $images_params['size_thumbnails'];

        $request->validate([
            'photo' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,gif',
                    'max:25000',
                    'dimensions:min_width=100,min_height=100,max_width=5000,max_height=5000',
            ],
        ]);
        if ($request->hasFile('photo')) {
            // Сохраняем файл на диск
            $image = $request->file('photo');
            $fileName = Str::random(40) . '.' . $image->getClientOriginalExtension();
            try {
                $fullpath = $image->storeAs($this->imagesDirectory, $fileName);
                // Изменяем размер изображения
                $imagePath = $this->createResizedImage($fullpath, $size_images, 'images');
                $this->createWaterMark($imagePath, $size_images, $watermark_text, 'images');
                // Создание миниатюры изображения
                $thumbnailPath = $this->createResizedImage($fullpath, $size_thumbnails, 'thumbnail');
                $this->createWaterMark($imagePath, $size_thumbnails, $watermark_text, 'thumbnail');
                Photo::create([
                    'razdel_id' => $request->razdel_id,
                    'filename' => $this->imagesDirectory. '/'.$fileName,
                    'thumbnail' => $this->thumbnailsDirectory. '/'.$fileName,
                    'title' => $request->title,
                    'description' => $request->description,
                ]);

            } catch (Throwable $e) {
                report($e);
                return false;
            } finally {
                return $this->show($request->razdel_id);
            }
        }
        return response()->json(['error' => 'Не удалось загрузить изображение'], 422);
    }
}
