<?php
namespace Ynotz\MediaManager\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Ynotz\MediaManager\Models\MediaItem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Ynotz\AccessControl\Models\Permission;

trait OwnsMedia
{
    public function mediaPermissions($property = null, $variant = null)
    {
        $query = $this->morphToMany(Permission::class, 'mediaowner', 'media_permissions', 'mediaowner_id', 'permission_id');
        if (isset($property)) {
            $query->where('property', $property);
        }
        if (isset($variant)) {
            $query->where('variant', $variant);
        }
        return $query->get();
    }

    public function media()
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'mediaitem_id');
    }

    public function attachMedia(MediaItem $mediaItem, string $property, array $customProps = []): void
    {
        $ulid = Str::ulid();
        if (count($customProps) > 0) {
            $this->media()->attach(
                $mediaItem,
                [
                    'id' => $ulid,
                    'property' => $property,
                    'custom_properties' => json_encode($customProps),
                    'created_by' => $this->id
                ]
            );
        } else {
            $this->media()->attach(
                $mediaItem,
                [
                    'id' => $ulid,
                    'property' => $property,
                    'created_by' => $this->id
                ]
            );
        }
    }

    public function addOneMediaFromEAInput(string $property, string $input)
    {
        if (strpos($input, config('mediaManager.ulid_separator')) === false) {
            $arr = explode('_::_', $input);
            $ulid = $arr[0];
            $fname = $arr[1];
            $tempDisk = config('mediaManager.temp_disk');
            $tempFolder = config('mediaManager.temp_folder');

            $filepath = Storage::disk($tempDisk)->path($tempFolder.'/'.$input);
            $mimeType = mime_content_type($filepath);
            $fileType = explode('/', $mimeType)[0];
            $size = Storage::disk($tempDisk)->size($tempFolder.'/'.$input);

            $destFolder = '';
            $destDisk = '';

            if (isset($this->getMediaStorage()[$property])) {
                $destFolder = $this->getMediaStorage()[$property]['folder'] ?? '';
                $destDisk = $this->getMediaStorage()[$property]['disk'] ?? '';
            }

            if ($destDisk == '' || $destFolder == '') {
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
            }

            $storagePath = $destFolder.'/'.$ulid.'/original/'.$fname;

            if (Storage::disk($tempDisk)->get($tempFolder.'/'.$input) == null) {
                throw new FileNotFoundException('Something went wrong. Couldn\'t save the '.$property.' file.');
            }

            Storage::disk($destDisk)->put(
                $destFolder.'/'.$ulid.'/original/'.$fname,
                Storage::disk($tempDisk)->get($tempFolder.'/'.$input)
            );

            Storage::disk($tempDisk)->delete($tempFolder.'/'.$input);
            $x = [
                'ulid' => $ulid,
                'filename' => $fname,
                'filepath' => $storagePath,
                'disk' => $destDisk,
                'type' => $fileType,
                'size' => $size, //size of the file in bytes
                'mime_type' => $mimeType,
            ];

            $mediaItem = MediaItem::create($x);

            $this->attachMedia($mediaItem, $property);

        } else {
            $ulid = str_replace(config('mediaManager.ulid_separator'), '', $input);
            $mediaItem = MediaItem::where('ulid', $ulid)->get()->first();

            if ($mediaItem != null) {
                $this->attachMedia($mediaItem, $property);
            }
        }

        // Do conversions if defined (check if conversions array exists)
        if (isset($this->getMediaVariants()[$property])) {
            if (isset($this->getMediaVariants()[$property]['process_on_upload']) && $this->getMediaVariants()[$property]['process_on_upload']) {
                //if queue available, queue job, else convert now
            }
        }
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
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'mediaitem_id')->where('property', $property)->get();
    }
    // public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false){}

    public function getSingleMedia(string $property): MediaItem
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'mediaitem_id')->where('property', $property)->get()->first();
    }

    public function getSingleMediaPath(string $property): string
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'media_item_id')
            ->where('property', $property)
            ->get()->first()->filepath;
    }

    public function getSingleMediaName(string $property): string
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'media_item_id')
            ->where('property', $property)
            ->get()->first()->filename;
    }

    public function getMediaVariants(): array
    {
        return [];
    }

    public function getMediaStorage(): array
    {
        return [];
    }
}
?>
