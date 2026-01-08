<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithConsoleEvents;
use App\Command\CsvReportCommand;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Console\Tester\CommandTester;
use Illuminate\Support\Facades\File;

class GameTest extends TestCase
{
    use WithConsoleEvents;

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * @var CommandTester
     */
    private $tester;

    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_question_command()
    {
       $this->artisan('configureer:game')
         ->expectsQuestion('Wil je de kaarten info in een CSV?', "Nee")
         ->assertExitCode(0);
    }

    public function test_export_command()
    {
        if (File::exists('test.csv')) {
            File::delete('test.csv');
        }

        $this->artisan('configureer:game')
            ->expectsQuestion('Wil je de kaarten info in een CSV?', "Ja")
            ->expectsQuestion("Hoe wil je het csv bestant noemen Of aan welk bestant wil je de kaart informatie toevoegen?", "test")
            ->expectsOutputToContain("De sets staan in het bestand test.csv.")
            ->assertExitCode(0);
        
    }

    public function test_export_command_when_file_exist()
    {
        $this->artisan('configureer:game')
            ->expectsQuestion('Wil je de kaarten info in een CSV?', "Ja")
            ->expectsQuestion("Hoe wil je het csv bestant noemen Of aan welk bestant wil je de kaart informatie toevoegen?", "test")
            ->expectsOutputToContain("De sets staan in het bestand test.csv.")
            ->assertExitCode(0);
        
    }

    public function test_export_command_file_create()
    {
        if (File::exists('test.csv')) {
            File::delete('test.csv');
        }

        $this->artisan('configureer:game')
            ->expectsQuestion('Wil je de kaarten info in een CSV?', "Ja")
            ->expectsQuestion("Hoe wil je het csv bestant noemen Of aan welk bestant wil je de kaart informatie toevoegen?", "test")
            ->expectsOutputToContain("De sets staan in het bestand test.csv.")
            ->assertExitCode(0);
        
        $this->assertTrue(file_exists('test.csv'));
    }

    public function test_export_command_file_header()
    {
        if (File::exists('test.csv')) {
            File::delete('test.csv');
        }

        $this->artisan('configureer:game')
            ->expectsQuestion('Wil je de kaarten info in een CSV?', "Ja")
            ->expectsQuestion("Hoe wil je het csv bestant noemen Of aan welk bestant wil je de kaart informatie toevoegen?", "test")
            ->expectsOutputToContain("De sets staan in het bestand test.csv.")
            ->assertExitCode(0);
        
        $content = file_get_contents('test.csv', false, null, 0, 41);
        $this->assertSame('id, name, scryfall_uri, released_at, uri,', $content);
    }

       public function test_export_command_file_data()
    {
        if (File::exists('test.csv')) {
            File::delete('test.csv');
        }

        $this->artisan('configureer:game')
            ->expectsQuestion('Wil je de kaarten info in een CSV?', "Ja")
            ->expectsQuestion("Hoe wil je het csv bestant noemen Of aan welk bestant wil je de kaart informatie toevoegen?", "test")
            ->expectsOutputToContain("De sets staan in het bestand test.csv.")
            ->assertExitCode(0);
        
        $content = file_get_contents('test.csv', false, null, 1218, 36);
        $this->assertSame('b423bb5a-eaac-4c1d-981a-1c635001fc5a', $content);
    }
}
