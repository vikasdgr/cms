<?php

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Auth\Permission;
use App\Models\Auth\Preference;
use App\Models\Masters\NoGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


function getArrKeyValue($arr, $key, $val = '')
{
    if (array_key_exists($key, $arr)) {
        return $arr[$key];
    } else {
        return $val;
    }
}

function chkFormulaCond($formula, $vararr, $amt)
{
    if ($formula->cformula != "Y") {
        return true;
    } else {
        if ($formula->ctype == "EQ" && array_key_exists($formula->cvarname, $vararr) && $vararr[$formula->cvarname] == $formula->cvalue) {
            return true;
        } elseif ($formula->ctype == "GE" && array_key_exists($formula->cvarname, $vararr) && $vararr[$formula->cvarname] >= $formula->cvalue) {
            return true;
        } elseif ($formula->ctype == "LE" && array_key_exists($formula->cvarname, $vararr) && $vararr[$formula->cvarname] <= $formula->cvalue) {
            return true;
        } elseif ($formula->ctype == "GT" && array_key_exists($formula->cvarname, $vararr) && $vararr[$formula->cvarname] > $formula->cvalue) {
            return true;
        } elseif ($formula->ctype == "LT" && array_key_exists($formula->cvarname, $vararr) && $vararr[$formula->cvarname] < $formula->cvalue) {
            return true;
        } else {
            return false;
        }
    }
}

function getPermissions()
{
    $user = auth()->user();
    if ($user->id == 1) {
        $permission = Permission::get();
    } else {
        $permission =  $user ?  $user->roles->first()->permissions()->get() :[];
    }
    return $permission->pluck('name')->toArray();
    $permissions = [];
    foreach (Permission::all() as $permission) {
        if ($permission->admin == "Y") {
            $permissions[$permission->name] = true;
        }
    }
    return $permissions;
}

function getFileUrl($path, $file = null)
{
    if (config('hr.upload_disk') == 's3') {
        return getS3TempUrl($path, 5);
    }
    $path = storage_path('/app' . $path);
}

function getS3TempUrl($path, $minutes = 5)
{

    $url = Str::startsWith($path, '/') ? Str::after($path, '/') : $path;
    // logger($url);
    if (Storage::disk('s3')->exists($url)) {
        return Storage::disk('s3')->temporaryUrl(
            $url,
            now()->addMinutes($minutes),
            [
                // 'ResponseContentType' => 'application/octet-stream',
                'Access-Control-Allow-Origin' => '*'
            ]
        );
    }
    return $path;
}

function showFile($path, $file = null, $thumbnail = false)
{
    if ($thumbnail) {
        $img_types = ['image/bmp', 'image/jpeg', '	image/gif', 'image/png'];
        if (!in_array($file->mime_type, $img_types)) {
            return showThumbnail($file);
        }
    }
    if (config('hr.upload_disk') == 's3') {
        $headers = [
            'Content-Disposition' => 'attachment; filename=' . $file->id . '.' . ($file->file_extension ? $file->file_extension : $file->file_ext),
            'Content-Type' => $file->mime_type,
            'Access-Control-Allow-Origin' => '*'
        ];
        if ($file->mime_type == 'application/pdf') {
            return getS3TempUrl($path, 5);
        }
        return response()->make(Storage::disk('s3')->get($path), 200, $headers);
    } else {
        $path = storage_path('/app' . $path);
    }
    return response()->file($path);
}
function showThumbnail($file)
{
    $pdf_types = ['application/pdf'];
    if (in_array($file->mime_type, $pdf_types)) {
        $path = public_path() . "/images/pdf.png";

    } else {
        $path = public_path() . "/images/other.png";
    }
    return response()->file($path);
}

function sharedAttachmentPath(){
    $path =  'shared-files';
    return $path;
}




function hr_round($amt, $rounding = 'N')
{
    if ($rounding == "Y") {
        $amt = round($amt);
    } elseif ($rounding == "U") {
        $amt = ceil($amt);
    } else {
        $amt = round($amt, 2);
    }
    return $amt;
}



function getInvalidIfscCode()
{
    return array(
        'ALLA' => 'ALLA',
        'SYNB' => 'SYNB',
        'BKDN' => 'BKDN',
        'VIJB' => 'VIJB',
        'STBP' => 'STBP',
        'ORBC' => 'ORBC',
        'CORP' => 'CORP',
        'ANDB' => 'ANDB',
        'UTBI' => 'UTBI',

    );
}

function isDate80orMoreYearsOld($date)
{
    if (time() > strtotime('+80 years', strtotime($date))) {
        return true;
    } else {
        return false;
    }
}


function getNextSeriesNumber($idname)
{
    $no = NoGenerator::firstOrCreate(['idname' => $idname, 'prefix' => '']);
    $no->increment('no', 1);
    return $no->no;
}



// Checks Ist Date is (G)reater/(S)mall or (E)qual to Other Date
function getDateComp($dt1, $dt2)
{
    if (strpos($dt1, '-') < 3) {
        $dt1 =  Carbon::createFromFormat('d-m-Y', $dt1);
    } else {
        $dt1 =  Carbon::createFromFormat('Y-m-d', $dt1);
    }
    if (strpos($dt2, '-') < 3) {
        $dt2 =  Carbon::createFromFormat('d-m-Y', $dt2);
    } else {
        $dt2 =  Carbon::createFromFormat('Y-m-d', $dt2);
    }
    $days = $dt1->diffInDays($dt2, false);
    if ($days == 0) {
        return "E";
    } elseif ($days > 0) {
        return "S";
    } else {
        return "G";
    }
}

function getNextMonthYear($month, $year, $mthyr = "M")
{
    $dt = "01 $month, $year";
    $date = Carbon::createFromFormat('d F, Y', $dt)->setTime(0, 0, 0);
    if ($mthyr == "M") {
        return $date->addMonth()->format('F');
    } else {
        return $date->addMonth()->format('Y');
    }
}

function getMonthYearNo($month, $year, $mthyr = "M")
{
    $dt = "01 $month, $year";
    $date = Carbon::createFromFormat('d F, Y', $dt)->setTime(0, 0, 0);
    if ($mthyr == "M") {
        return floatval($date->format("m"));
    } else {
        return floatval($date->format("Y"));
    }
}

function getMonthDate($month, $year, $start_end = "S")
{
    $dt = "01 $month, $year";
    $date = Carbon::createFromFormat('d F, Y', $dt)->setTime(0, 0, 0);
    if ($start_end == "E") {
        $date = $date->addMonth()->addDays(-1);
    }
    return $date->format('d-m-Y');
}
function userPreferencesSidebar()
{
    if (class_exists(Preference::class) && Auth::check())
        $preference = Preference::where('user_id', auth()->user()->id)->where('para_name', 'sidebar')->first();
    else
        $preference = null;
    return $preference ?  $preference->para_value : 'open';
}
