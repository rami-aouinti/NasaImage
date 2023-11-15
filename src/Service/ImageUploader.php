<?php

declare(strict_types=1);

/**
 * (c) Rami Aouinti <rami.aouinti@gmail.com>
 **/

namespace App\Service;

use DateTime;

/**
 * Class ImageUploader
 */
class ImageUploader
{
    public function __construct(
        private string $targetDirectory,
        private readonly string $nasaApiKey,
    ) {
    }

    public function uploadFromUrlToFolder(string $imageName,string $folder, ?string $date)
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $date);
        $month = $datetime->format('m');
        $day = $datetime->format('d');
        $year = $datetime->format('y');

        $images = "https://api.nasa.gov/EPIC/api/natural/date/{$year}-{$month}-{$day}?api_key={$this->nasaApiKey}Y";
        $meta = file_get_contents($images);
        $arr = json_decode($meta);

        foreach($arr as $item) {
            $name = $item->image . '.png';
            $archive = "https://epic.gsfc.nasa.gov/archive/natural/{$year}/{$month}/{$day}/png/";

            $source = $archive . $name;
            $destination = $this->targetDirectory .'/' . $folder . '/' . $year . '/' . $month . '/' . $day . '/' . $name;
            copy($source, $destination);

        }
    }
}
