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
use Throwable;

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
    
    public function questionCartInfo($anser, $verbosity = null){
        $anser = select(
            label: 'Wil je de kaarten info in een CSV?',
            options: ['Ja', 'Nee']
        );

        return $anser;
    }

    public function API()
    {
        try
        {
            $response = Http::get('https://api.scryfall.com/cards/search?q=c%3Awhite+mv%3D1')->throw();
            return $response;
        }
        catch (Throwable $e)
        {
            $this->error('Er is een fout opgetreden: ' . $e->getMessage());
            die;
        }
    }

    public function convertDate($array, $number, $arrayReleaseds)
    {
        for ($row = 0; $row < $number; $row++) {
            $arrayReleaseds[$row] = $array[$row];
        }

        $convertDate = function ($arrayReleased) {
            return date("Y-m-d", strtotime($arrayReleased["released_at"])); 
        };

        $sortableDates = array_map($convertDate, $arrayReleaseds);
        array_multisort($sortableDates, SORT_ASC, $arrayReleaseds);

        return $arrayReleaseds;
    }

    public function heder($file, $colomNames)
    {
        for ($i = 0; $i < count($colomNames); $i++)
        {
            try
            {
                file_put_contents($file, $colomNames[$i]. ", " , FILE_APPEND | LOCK_EX);
            }
            catch (Throwable $e)
            {
                $this->error('Er is een fout opgetreden: ' . $e->getMessage());
                die;
            }
        }
    }

    public function data($convertDate, $number, $file, $colomNames)
    {
        for ($row = 0; $row < $number; $row++)     
        {
            try
            {
                file_put_contents($file, "\n" , FILE_APPEND | LOCK_EX);        
            }
            catch (Throwable $e)
            {
                $this->error('Er is een fout opgetreden: ' . $e->getMessage());
                die;
            }

            for ($i = 0; $i < count($colomNames); $i++) { 
                $current = $convertDate[$row][$colomNames[$i]];

                try
                {
                    file_put_contents($file,  $current . ", ", FILE_APPEND | LOCK_EX);
                }
                catch (Throwable $e)
                {
                    $this->error('Er is een fout opgetreden: ' . $e->getMessage());
                    die;
                }
            }
        }
    }

    public function handle()
    {
        $anser = "";
        $response = "";
        $number = 0;
        $arrayReleaseds = [];
        $message = $this->questionCartInfo($anser);
        
        if ($message == "Ja") {
            $fileName = text('Hoe wil je het csv bestant noemen Of aan welk bestant wil je de kaart informatie toevoegen?');
            
            $API = $this->API($response);
            
            if ($API['data'])
            {
                $array = $API['data'];
                $number = count($array);
                $convertDate = $this->convertDate($array, $number, $arrayReleaseds);            
                
                $file = $fileName . '.csv';
                $colomNames = ["id", "name", "scryfall_uri", "released_at", "uri"];

                $this->heder($file, $colomNames);
                
                $this->data($convertDate, $number, $file, $colomNames);

                $this->info('De sets staan in het bestand '. $file . ".");
            } else {
                $this->error("Data wordt niet goed verwerkt.");
            }
        } 
    }
}