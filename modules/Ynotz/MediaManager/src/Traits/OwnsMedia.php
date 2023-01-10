<?php
namespace Ynotz\MediaManager\Traits;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Ynotz\MediaManager\Helpers\MediaHelper;
use Ynotz\MediaManager\Models\MediaItem;

trait OwnsMedia
{
    public function media()
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_items_owners', 'mediaowner_id', 'mediaitem_id');
    }

    public function attachMedia(MediaItem $mediaItem, string $property, array $customProps = []): void
    {
        if (count($customProps) > 0) {
            $this->media()->attach(
                $mediaItem,
                [
                    'property' => $property,
                    'custom_properties' => json_encode($customProps)
                ]
            );
        } else {
            $this->media()->attach(
                $mediaItem,
                ['property' => $property]
            );
        }
    }

    public function addOneMediaFromEAInput(string $property, string $input)
    {
        $arr = explode('_', $input);
        $ulid = $arr[0];
        $fname = $arr[1];

        // $file = Storage::get($srcFolder.'/'.$input);


        $tempDisk = config('mediaManager.temp_disk');
        $tempFolder = config('mediaManager.temp_folder');

        $filepath = Storage::disk($tempDisk)->path($tempFolder.'/'.$input);

        $mimeType = mime_content_type($filepath);
        $mimeType = mime_content_type($filepath);
        $fileType = explode('/', $mimeType)[0];
        $size = Storage::size($tempFolder.'/'.$input);


        $destFolder = '';
        $destDisk = '';

        switch($fileType) {
            case 'image':
                $destFolder = config('mediaManager.images_folder');
                $destDisk = config('mediaManager.images_disk');
                break;
            case 'video':
                $destFolder = config('mediaManager.videos_folder');
                $destDisk = config('mediaManager.videos_disk');
                break;
            default:
                $destFolder = config('mediaManager.files_folder');
                $destDisk = config('mediaManager.files_disk');
                break;
        }

        $storagePath = $destFolder.'/'.$ulid.'/original/'.$fname;

        if (Storage::disk($tempDisk)->get($tempFolder.'/'.$input) == null) {
            throw new FileNotFoundException('Something went wrong. Couldn\'t save the '.$property.' file.');
        }

        // MediaHelper::moveMedia(
        //     $tempDisk,
        //     $tempFolder,
        //     $input,
        //     $destDisk,
        //     $destFolder.'/'.$ulid.'/original',
        //     $fname
        // );
        Storage::disk($destDisk)->put(
            $destFolder.'/'.$ulid.'/original/'.$fname,
            Storage::disk($tempDisk)->get($tempFolder.'/'.$input)
        );

        Storage::disk($tempDisk)->delete($tempFolder.'/'.$input);

        // Storage::disk($destDisk)->put(
        //     $destFolder.'/'.$fname,
        //     Storage::disk($tempDisk)->get($tempFolder.'/'.$input)
        // );

        // Storage::move($srcFolder.'/'.$input, $storagePath);

        $mediaItem = MediaItem::create(
            [
                'ulid' => $ulid,
                'filename' => $fname,
                'filepath' => $storagePath,
                'disk' => $destDisk,
                'type' => $fileType,
                'size' => $size,
                'mime_type' => $mimeType,
            ]
        );

        $this->attachMedia($mediaItem, $property);
        // Do conversions if defined (check if conversions array exists)
    }

    public function addMediaFromEAInput(string $property, array|string $vals): void
    {
        if (is_array($vals)) {
            foreach ($vals as $input) {
                $this->addOneMediaFromEAInput($property, $input);
            }
        } else {
            $this->addOneMediaFromEAInput($property, $vals);
        }
    }

    public function getAllMedia(string $property): Collection
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_items_owners', 'mediaowner_id', 'mediaitem_id')->where('property', $property)->get();
    }
    // public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false){}

    public function getSingleMedia(string $property): MediaItem
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_items_owners', 'mediaowner_id', 'mediaitem_id')->where('property', $property)->get()->first();
    }

    public function getSingleMediaPath(string $property): string
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_items_owners', 'mediaowner_id', 'media_item_id')
            ->where('property', $property)
            ->get()->first()->filepath;
    }

    public function getSingleMediaName(string $property): string
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_items_owners', 'mediaowner_id', 'media_item_id')
            ->where('property', $property)
            ->get()->first()->filename;
    }
}
?>
