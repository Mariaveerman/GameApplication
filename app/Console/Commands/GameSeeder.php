<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\error;
use function Laravel\Prompts\table;
use function Laravel\Prompts\spin;
use Illuminate\Support\Facades\Http;
use OzdemirBurak\JsonCsv\File\Json;
use Illuminate\Http\Client\Response;

class GameSeeder extends Command
{
        /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'configureer:game';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'bestand configureren naar CSV';
    
    public function handle()
    {
        $anser = select(
            label: 'Wil je de kaarten info in een CSV?',
            options: ['Ja', 'Nee']
        );


        if ($anser == "Ja") {
            $fileName = text('Hoe wil je het csv bestant noemen Of aan welk bestant wil je de kaart informatie toevoegen?');

            $response = Http::get('https://api.scryfall.com/cards/search?q=c%3Awhite+mv%3D1');
            
            $array = $response['data'];
        
            $number = count($array);
            
            $arrayReleaseds = [];

            for ($row = 0; $row < $number; $row++) {

                $arrayReleaseds[$row] = $array[$row];
            }

            $convertDate = function ($arrayReleased) {
            return date("Y-m-d", strtotime($arrayReleased["released_at"])); 
            };

            $sortableDates = array_map($convertDate, $arrayReleaseds);
            
            array_multisort($sortableDates, SORT_ASC, $arrayReleaseds);

            $file = $fileName . '.csv';
            $colomNames = ["id", "name", "scryfall_uri", "released_at", "uri"];
            for ($i = 0; $i < count($colomNames); $i++)
            {
                file_put_contents($file, $colomNames[$i]. ", " , FILE_APPEND | LOCK_EX);
            }
            
            for ($row = 0; $row < $number; $row++)     
            {
                file_put_contents($file, "\n" , FILE_APPEND | LOCK_EX);

                 for ($i = 0; $i < count($colomNames); $i++) {           
                    $current = $arrayReleaseds[$row][$colomNames[$i]];

                    file_put_contents($file,  $current . ", ", FILE_APPEND | LOCK_EX);
                }
            }
            $this->info('de sets staan in het bestand '. $file . ".");
        } 
    }
}