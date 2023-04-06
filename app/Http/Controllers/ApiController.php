<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Response;
use Thread;

class ApiController extends Controller
{
    public function DatabaseIOPerformanceTest()
    {
        // Create and populate a list of 2000 Products
        $products = [];
        for ($i = 1; $i <= 1000; $i++) {
        $products[] = [
            'name' => "Product$i",
            'description' => "Description for Product$i",
            'price' => $i * 10
         ];
        }

        // Check if Products table exists and delete any existing data in the table
        if (Schema::hasTable('PRODUCT')) {
            DB::table('PRODUCT')->delete();
        }

        $batches = array_chunk($products, 100);

        // Start timer
        $start_time = microtime(true);

        // Insert the list of products into the database using Laravel's query builder
        foreach($batches as $batch) {
            DB::table('PRODUCT')->insert($batch);
        }

        // Select all PRODUCT rows from the database and convert them to an array
        $result = DB::table('PRODUCT')->get()->toArray();

        // Stop timer
        $end_time = microtime(true);
        $elapsedTime = $end_time - $start_time;

        return $elapsedTime;
    }

    public function DiscIOPerformanceTest()
    {
        //Deletes "integers.csv" file if it already exists
        if (File::exists("integers.csv")) {
            File::delete("integers.csv");
        }

        //start timer
        $start_time = microtime(true);

        //size of integers array
        $size = 10000;

        $integers = [];
        $integersList = [];

        //populate integers array of size 10000 with random numbers ranging from 0 to 1000
        for ($i = 0; $i < $size; $i++) {
            $integers[$i] = rand(0, 1000);
        }

        //use Storage put() method to write the integers array into the "integers.csv" file
        Storage::put("integers.csv", implode("\n", $integers));

        //use Storage get() method to read the "integers.csv" file and add it to the integersList line by line
        $integersFile = Storage::get("integers.csv");
        $integersArray = explode("\n", $integersFile);

        foreach ($integersArray as $integer) {
            array_push($integersList, intval($integer));
        }

        $end_time = microtime(true);

        $elapsedTime = $end_time - $start_time; // in milliseconds

        return $elapsedTime;
    }

    public function GarbageCollectionPerformanceTest()
    {
        $objectSizeInMB = 100;
        $numIterations = 10;

        // Allocate a large object in memory
        $largeObject = str_repeat('1', $objectSizeInMB * 1024 * 1024);

        // Starts timer
        $start_time = microtime(true);

        // Force garbage collection and measure the time it takes
        for ($i = 0; $i < $numIterations; $i++) {
            gc_collect_cycles();
        }

        $end_time = microtime(true);

        $elapsedTime = $end_time - $start_time;

        return $elapsedTime;
    }

    public function ThreadPerformanceTest()
    {
        $numThreads = 4;
        $numIterations = 10000000;

        // Create an array of 4 threads
        $threads = [];

        // Start the timer
        $start_time = microtime(true);

        // Start the threads
        for ($i = 0; $i < $numThreads; $i++) {
            $threads[$i] = new class($numIterations) extends Thread {
                private $numIterations;

                public function __construct($numIterations)
                {
                    $this->numIterations = $numIterations;
                }

                public function run()
                {
                    for ($j = 0; $j < $this->numIterations; $j++) {
                        // Perform some CPU-bound work
                        $result = sqrt($j * 1000);
                    }
                }
            };
            $threads[$i]->start();
        }

        // Wait for all threads to complete
        for ($i = 0; $i < $numThreads; $i++) {
            $threads[$i]->join();
        }

        $end_time = microtime(true);

        $elapsedTime = $end_time - $start_time;

        return $elapsedTime;
    }

}
