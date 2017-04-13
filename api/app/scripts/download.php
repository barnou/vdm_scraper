<?php

require __DIR__ . '/../../vendor/autoload.php';

use ProgressBar\Manager;
use Goutte\Client;

function cleanString($string) {
    $string = trim($string);
    //$string = str_replace('.', '', $string); // remove dots
    //$string = str_replace(' ', '', $string); // remove spaces
    $string = str_replace("\t", '', $string); // remove tabs
    $string = str_replace("\n", '', $string); // remove new lines
    $string = str_replace("\r", '', $string); // remove carriage return
    return $string;
}

// list of months to connect regexp result to month number
$months = ['janvier'=> '01', 'février'=> '02', 'mars'=> '03', 'avril'=> '04', 'mai'=> '05', 'juin'=> '06','juillet'=> '07', 'août'=> '08', 'septembre'=> '09', 'octobre'=> '10', 'novembre'=> '11', 'décembre'=> '12'];

$i = 1;
$wantedSize = 200;
$running = true;
$vdmArray = [];
$errors = [];

$progressBar = new Manager(0, $wantedSize);
$client = new Client();

do {
    
    // Get page of vdm incremented by counter
    $crawler = $client->request('GET', 'http://www.viedemerde.fr/?page='.$i);
    $status_code = $client->getResponse()->getStatus();
    

    if($status_code != 200){
        $errors[] = "Page status code is $status_code. It seems there is a problem with viedemerde.fr";
        break;
    }
    
    // We parse dom element in order to put our cursor on the good element
    $firstColumn = $crawler->filter('#content > .row')->first();

    // Then we iterate over all articles
    $firstColumn->filter('article > .panel > .panel-body')->each(function ($node, $index) use (&$vdmArray, &$running, $wantedSize, $months, &$errors) {
        if(count($node->filter('iframe')) == 0) {

            $vdmNode = $node->filter('.panel-content')->eq(1);

            // We try to get panel-content node, if there is not we jump on another article
            if(count($vdmNode) > 0) {
                $vdmObject = new stdClass();

                $matches = array();
                $dateArray = array();

                $userNode = $node->children()->last()->text();
                $userNode = str_replace("\n", '', $userNode);

                preg_match('/Par(.+)\/(.+)\/(.*)$/i', $userNode, $matches);

                $date = trim($matches[2]);
                $placeArray = explode('-',$matches[3]);

                preg_match('/[A-z]+ (\d+) ([A-zé]+) (\d+) ([01]?\d|2[0-3]):([0-5]?\d)/i', $date, $dateArray);
                
                $day = sprintf('%02d', intval($dateArray[1]));
                $month = $months[$dateArray[2]];
                $year = $dateArray[3];
                $hours = $dateArray[4];
                $minutes = $dateArray[5];

                $timestamp = strtotime("$year-$month-$day $hours:$minutes CEST");
                
                $vdmObject->datelog = $timestamp;
                $vdmObject->username = trim($matches[1]," -");
                $vdmObject->country = strlen(trim($placeArray[1])) > 0 ? trim($placeArray[1]):null;
                $vdmObject->city = strlen(trim($placeArray[0])) > 0 ? trim($placeArray[0]): null;
                $vdmObject->vdm = cleanString($vdmNode->filter('p > a')->text());

                // If we have enought vdm posts, then we stop
                if(count($vdmArray) >= $wantedSize) {
                    $running = false;
                } else {
                    // else we push object in vdm array
                    array_push($vdmArray, $vdmObject);
                }
            }
        }
    });
    $progressBar->update(count($vdmArray));
    $i++;
} while($running && count($vdmArray < $wantedSize));

if(count($errors) > 0) {
    foreach ($errors as $error) {
        echo $error."\n";
    }
} else {
    var_dump(json_encode($vdmArray));
}