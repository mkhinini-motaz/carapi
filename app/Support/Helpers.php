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


/**
 * Defines a callable that creates an Eloquent where clause to find entities that occurs in period. The queried entity
 * must have a start date and an end date column, end date may be null , applying order is optional.
 *
 * Return value can be used as follow :
 *     \Illuminate\Database\Query\Builder::where(whereOccursInPeriod(...))
 *     \Illuminate\Database\Eloquent\Model::with(['relationName' => whereOccursInPeriod(...)])
 *
 * @param string $startsAtColumnName  must be a valid column name in the entity using this function
 * @param string $endsAtColumnName    must be a valid column name in the entity using this function
 * @param int $periodStartsAt         UNIX timestamp
 * @param int $periodEndsAt           UNIX timestamp
 * @param int $periodEndsAt           UNIX timestamp
 * @param bool $withInnerSearch
 * @param bool $withOuterSearch
 *
 * @return \Closure
 */
function occursInPeriod($startsAtColumnName, $endsAtColumnName, $periodStartsAt, $periodEndsAt, $withInnerSearch = true, $withOuterSearch = true)
{
    if (! $withInnerSearch && ! $withOuterSearch) {
        throw new \InvalidArgumentException('At least one of $withInnerSearch or $withOuterSearch should be true');
    }

    return function ($query) use ($startsAtColumnName, $endsAtColumnName, $periodStartsAt, $periodEndsAt, $withInnerSearch, $withOuterSearch) {
        $query->where(function ($mainQuery) use ($startsAtColumnName, $endsAtColumnName, $periodStartsAt, $periodEndsAt, $withInnerSearch, $withOuterSearch) {
            if ($withInnerSearch) {
                $mainQuery->where(function ($firstQuery) use ($startsAtColumnName, $periodStartsAt, $periodEndsAt) {
                    // starts within period
                    $firstQuery->where($startsAtColumnName, '>=', $periodStartsAt)
                        ->where($startsAtColumnName, '<', $periodEndsAt);

                })->orWhere(function ($secondQuery) use ($endsAtColumnName, $periodStartsAt, $periodEndsAt) {
                    // ends within period
                    $secondQuery->where($endsAtColumnName, '>=', $periodStartsAt)
                        ->where($endsAtColumnName, '<', $periodEndsAt);

                });
            }

            if ($withOuterSearch) {
                $mainQuery->orWhere(function ($thirdQuery) use ($startsAtColumnName, $endsAtColumnName, $periodStartsAt, $periodEndsAt) {
                    // starts before period, ends after it
                    $thirdQuery->where($startsAtColumnName, '<=', $periodStartsAt)
                        ->where($endsAtColumnName, '>=', $periodEndsAt);
    
                })->orWhere(function ($fourthQuery) use ($startsAtColumnName, $endsAtColumnName, $periodEndsAt) {
                    // starts before period's end endsAtColumn is null
                    $fourthQuery->where($startsAtColumnName, '<', $periodEndsAt)
                        ->whereNull($endsAtColumnName);
                });
            }
        });
    };
}

function occursInPeriodSql($startsAtColumnName, $endsAtColumnName, $periodStartsAt, $periodEndsAt, $withInnerSearch = true, $withOuterSearch = true) {
    if (! $withInnerSearch && ! $withOuterSearch) {
        throw new \InvalidArgumentException('At least one of $withInnerSearch or $withOuterSearch should be true');
    }

    $periodStartsAt = (int) $periodStartsAt;
    $periodEndsAt = (int) $periodEndsAt;
    $output = '((';
    if ($withInnerSearch) {
        $output .= '("' . $startsAtColumnName . '" >= ' . $periodStartsAt . ' and "' . $startsAtColumnName . '" < ' . $periodEndsAt .
            ') or ("' . $endsAtColumnName . '" >= ' . $periodStartsAt . ' and "' . $endsAtColumnName . '" < ' . $periodEndsAt . ')';
    }

    if ($withOuterSearch) {
        if ($withInnerSearch) {
            $output .= ' or ';
        }
        $output .= '("' . $startsAtColumnName . '" <= ' . $periodStartsAt . ' and "' . $endsAtColumnName . '" >= ' . $periodEndsAt .
            ') or ("' . $startsAtColumnName . '" < ' . $periodEndsAt . ' and "' . $endsAtColumnName . '" is null)';
    }

    $output .= '))';

    return $output;
}

function availabilityWithinPeriod(int $periodStart, int $periodEnd, int $busyPeriodStart, int $busyPeriodEnd) {
    // Busy inside period
    if ($periodStart < $busyPeriodStart && $periodEnd > $busyPeriodEnd) {
        return [[ $periodStart, $busyPeriodStart], [ $busyPeriodEnd, $periodEnd]];
    }
    //Busy in period start
    if ($periodStart > $busyPeriodStart && $periodEnd > $busyPeriodEnd) {
        return [[ $busyPeriodEnd, $periodEnd]];
    }
    // Busy in period end
    if ($periodStart < $busyPeriodStart && $periodEnd < $busyPeriodEnd) {
        return [[ $periodStart, $busyPeriodStart]];
    }

    return [[ $periodStart, $periodEnd ]];
}