<?php

include 'vendor/autoload.php';

use League\Csv\Reader;
use League\Csv\Writer;

$csv = Reader::createFromPath('TRSQ.csv');
$csvWriter = Writer::createFromFileObject(new SplFileObject('TSRQUpdate.csv', 'w'));

$csvData = $csv->fetchAssoc(['id', 'no', 'quad', 'tr', 't', 'td', 'r', 'rd', 's', 'q']);

foreach ($csvData as $line) {

    $parameter = array(
        'IN',
        2,
        str_replace(' ', '',trim($line['t'])),
        0,
        str_replace(' ', '',trim($line['td'])),
        str_replace(' ', '',trim($line['r'])),
        0,
        str_replace(' ', '',trim($line['rd'])),
        str_replace(' ', '',trim($line['s'])),
        str_replace(' ', '',trim($line['q'])),
        0
    );

    $url = 'http://www.geocommunicator.gov/TownshipGeocoder/TownshipGeocoder.asmx/GetLatLon?TRS=' . implode(',', $parameter);
    dump($url);
    $document = new \SimpleXMLElement(file_get_contents($url));

    if ($document) {
        if ((string)$document->Message === "Ok") {

            $points = array();
            $center_points = array();

            $data = $document->Data;

            $simplexml = new \SimpleXMLElement($data);

            $center_points = explode(',', (string)$simplexml->channel->item[0]->description);

            foreach ($center_points as $point) {
                preg_match("/\([^)]+\)/", $point, $output);
                $points[] = preg_replace('/\(|\)/','',$output[0]);
            }

            $center_points = $points;

            $document = new \DOMDocument();
            $document->loadXML($data);

            foreach($document->getElementsByTagName('polygon') as $polygon) {
                if ($polygon->hasChildNodes()) {
                    $points = explode(',', trim($polygon->firstChild->nodeValue));
                }
            }

            $points = array_merge($center_points, $points);

            $array_of_data = array_merge($line, $points);

            $csvWriter->insertOne($array_of_data);
        } else {
            array_push($line, $url);
            $csvWriter->insertOne($line);
        }
    } else {
        array_push($line, $url);
        $csvWriter->insertOne($line);
    }

}