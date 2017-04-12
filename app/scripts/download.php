<?php

require __DIR__ . '/../../vendor/autoload.php';

use ProgressBar\Manager;
use ProgressBar\Registry;
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

$i = 1;
$wantedSize = 200;
$running = true;
$vdmArray = [];

$progressBar = new Manager(0, $wantedSize);
$client = new Client();

do {
    $crawler = $client->request('GET', 'http://www.viedemerde.fr/?page='.$i);

    $firstColumn = $crawler->filter('#content > .row')->first();

    $firstColumn->filter('article > .panel > .panel-body')->each(function ($node, $index) use (&$vdmArray, &$running, $wantedSize, $progressBar) {
        if(count($node->filter('iframe')) == 0) {

            $vdmNode = $node->filter('.panel-content')->eq(1);
            if(count($vdmNode) > 0) {
                $matches = array();
                $userNode = $node->children()->eq(2)->text();
                $userNode = str_replace("\n", '', $userNode);

                preg_match('/Par(.+)\/(.+)\/(.*)$/i', $userNode, $matches);

                $vdmObject = new stdClass();

                $vdmObject->username = trim($matches[1]," -");
                $vdmObject->date = trim($matches[2]);
                $placeArray = explode('-',$matches[3]);

                $vdmObject->country = strlen(trim($placeArray[1])) > 0 ? trim($placeArray[1]):null;
                $vdmObject->city = strlen(trim($placeArray[0])) > 0 ? trim($placeArray[0]): null;

                //$userNode = $node->filter('div')->eq(2);
                //var_dump($userNode->text());
                $vdmObject->vdm = cleanString($vdmNode->filter('p > a')->text());
                /*$fmt = new IntlDateFormatter(
                    'fr_FR',
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    'EEEE dd LLLL yyyy'
                );
                var_dump($t = $fmt->parse($date), date('d/m/Y', $t));*/
                
                if(count($vdmArray) >= $wantedSize) {
                    $running = false;
                } else {
                    array_push($vdmArray, $vdmObject);
                }
            }
        }
    });
    $progressBar->update(count($vdmArray));
    $i++;
} while($running && count($vdmArray < $wantedSize));

echo "downloaded\n";