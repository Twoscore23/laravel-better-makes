<?php

use Illuminate\Filesystem\Filesystem;

function getPlural(string $string)
{
    return $string[-1] == 'y'
        ? (
            preg_match("/[aeiou]/", $string[-2]) 
                ? $string . 's'
                : substr_replace($string, 'ies', -1)
        )
        : (
            $string[-1] == 's' 
                ? $string . 'es'
                : $string . 's'
        );
}

function writeToFile(string $file, string $dir, string $contents, string $fileType)
{
    $files = new Filesystem;
    if($files->isDirectory($dir)){
        if($files->isFile($file))
        {
            throw new Exception("File Already exists!");
        }
        
        if(!$files->put($file, $contents))
        {
            throw new Exception('Something went wrong!');
        }
        
        return "$fileType generated!";
    }
    else{
        $files->makeDirectory($dir, 0777, true, true);

        if(!$files->put($file, $contents))
        {
            throw new Exception('Something went wrong!');
        }

        return "$fileType generated!";
    }
}

function getRelationship(string $column)
{
    if (!str_contains($column, '_id'))
    {
        return null;
    }

    return substr($column, 0, strpos($column, '_id'));
}

function formatSnakeCaseToStartCase(string $string)
{
    $strArr = explode('_', $string);
    $strStartCase = ucfirst(array_shift($strArr));
    $strStartCase .= implode("", 
        array_map(function (string $str) {
            return ucfirst($str);
        }, $strArr)
    );

    return $strStartCase;
}

function formatSnakeCaseToCamelCase(string $string)
{
    return lcfirst(formatSnakeCaseToStartCase($string));
}

function formatStartCaseToSnakeCase(string $string)
{
    $strArr = str_split($string);
    foreach ($strArr as $index=>$char)
    {
        if (ctype_upper($char) && $index != 0)
        {
            array_splice($strArr, $index, 1, "_$char");
        }
    }

    return strtolower(implode($strArr));
}