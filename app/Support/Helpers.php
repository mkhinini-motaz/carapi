<?php

namespace App\Support\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * @param UploadedFile $file
 * @param string       $type
 *
 * @return null|string
 */
// TODO install imagick
function uploadFile(UploadedFile $file, $type = 'default')
{
    if (! $file->isValid()) {
        \Log::error("uploadFile error : file is not valid");
        return null;
    }

    $customString = time();
    $uploadAttributes = config("mimetypes.$type");
    $path = config("paths.$type");
    if (Str::contains($path, '%s')) {
        $path = str_replace('%s', substr($customString, -1) . '/' . $customString, $path);
    }
    $storagePath = $path;

    $mimeType = $file->getMimeType();
    if (! isset($uploadAttributes[$mimeType])) {
        \Log::error('File mime type not found. Type :" ' . $type . ' " for mimetype :" ' . $mimeType . ' "');
        return null;
    }

    $extension = $uploadAttributes[$mimeType];
    $name = Str::random(10) . '_' . time();
    $fullName = $name . '.' . $extension;

    if (! $file->move($storagePath, $fullName)) {
        return null;
    }

    if (isset($uploadAttributes['size'])) {
        $dimension = explode('x', $uploadAttributes['size']);
        if (count($dimension) == 2) {
            $image = \Image::make($storagePath . '/' . $fullName);
            $image->resize($dimension[0], null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($storagePath . '/' . $fullName);
            unset($image);
        }
    }

    if (isset($uploadAttributes['max_size'])) {
        $dimension = explode('x', $uploadAttributes['max_size']);
        if (count($dimension) == 2) {
            $image = \Image::make($storagePath . '/' . $fullName);
            if ($dimension[0] < $image->getWidth() || $dimension[1] < $image->getHeight()) {
                $useWidth = ($image->getWidth() / $dimension[0]) > ($image->getHeight() / $dimension[1]);
                $image->resize($useWidth ? $dimension[0] : null, $useWidth ? null : $dimension[1], function ($constraint) {
                    $constraint->aspectRatio();
                })->save($storagePath . '/' . $fullName);
            }
            unset($image);
        }
    }

    if (isset($uploadAttributes['thumbnails'])) {
        foreach ($uploadAttributes['thumbnails'] as $suffix => $size) {
            $dimension = explode('x', $size);
            if (count($dimension) != 2) {
                continue;
            }
            $image = \Image::make($storagePath . '/' . $fullName);
            $image->resize($dimension[0], null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($storagePath . '/' . $name . '_' . $suffix . '.' . $extension);
        }
    }

    return $path . '/' . $fullName;
}

/**
 * Query builder helper for the Like operator
 *
 * Return value can be used as follow :
 *     \Illuminate\Database\Query\Builder::where(fieldsLike(...))
 *     \Illuminate\Database\Eloquent\Model::with(['relationName' => fieldsLike(...)])
 *
 * @param string $term
 * @param array ...$fields
 *
 * @return \Closure
 */
function fieldsLike($term, ...$fields)
{
    return function ($query) use ($term, $fields) {
        foreach ($fields as $field) {
            $query->orWhere($field, 'LIKE', '%' . $term . '%');
        }
    };
}

/**
 * Query builder helper for first name and last name search with the Like operator
 *
 * Return value can be used as follow :
 *     \Illuminate\Database\Query\Builder::where(firstNameLastNameLike(...))
 *     \Illuminate\Database\Eloquent\Model::with(['relationName' => firstNameLastNameLike(...)])
 *
 * @param string $term
 *
 * @return \Closure
 */
function firstNameLastNameLike($term)
{
    return function ($query) use ($term) {
        $query->whereRaw("CONCAT(last_name, ' ', first_name) LIKE '%$term%'")
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE '%$term%'");
    };
}

/**
 * Query builder helper to get today row
 *
 * @param array  $period [ start, end ]
 * @param string $columnName
 *
 * @return \Closure
 */
function columnInPeriod($period, $columnName)
{
    return function ($query) use ($period, $columnName) {
        $query->where($columnName, '>', $period[0])
            ->where($columnName, '<', $period[1]);
    };
}

/**
 * @param string $str
 * @param string $date
 *
 * @return false|string
 */
function strToDate(string $str, string $date)
{
    return date('d-m-Y H:i', strtotime($str, strtotime($date)));

}
