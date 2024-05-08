<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$base_url = 'https://is.stuba.sk/pracoviste/prehled_temat.pl?lang=sk;pracoviste=';
$boolValue = true;
$options = array(642, 548, 549, 550, 816, 817, 818, 356);

if(isset($_GET['option']) && $_GET['option'] == '1'){

    $boolValue = true;
    $options = array(642, 548);
}
else if(isset($_GET['option']) && $_GET['option'] == '2'){

    $boolValue = false;
    $options = array(549, 550);
}
else if(isset($_GET['option']) && $_GET['option'] == '3'){

    $boolValue = true;
    $options = array(816, 817);
}
else if(isset($_GET['option']) && $_GET['option'] == '4'){

    $boolValue = false;
    $options = array(818, 356);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$htmls = array();

foreach ($options as $option) {
    
    $url = $base_url . $option;
    curl_setopt($ch, CURLOPT_URL, $url);
    $htmls[] = curl_exec($ch);
}

curl_close($ch);

$doc = new DOMDocument();

$data = array();
$dataAhref = array();
$row = array();

$counterHtml = 0;

$mh = curl_multi_init();
$handles = array();

foreach ($htmls as $html) {

    @$doc->loadHTML($html);

    $table = $doc->getElementsByTagName('table');
    $trs = $table->item(3)->getElementsByTagName('tr');

    $counter = 0;
    
    foreach ($trs as $tr) {

        if ($counter == 0) {
            $counter++;
            continue;
        }

        if($boolValue){

            $tds = $tr->getElementsByTagName('td');

                if(($tds->item(9)->textContent == '1 / 1') || ($tds->item(9)->textContent == '2 / 1')){
                    continue;
                }            

                $row['nazov'] =  $tds->item(2)->textContent;;
                $row['typ'] = $tds->item(1)->textContent;
                $row['veduci'] = $tds->item(3)->textContent;
                $row['garpra'] = $tds->item(4)->textContent;
                $row['program'] = $tds->item(5)->textContent;
                $row['zameranie'] = $tds->item(6)->textContent;

                $href = $tds->item(8);
                $a = $href->getElementsByTagName('a')->item(0)->getAttribute('href');

                $urlBaseAddr = 'https://is.stuba.sk';
                $dz = curl_init();
                curl_setopt($dz, CURLOPT_RETURNTRANSFER, true);
                $urlAddr = $urlBaseAddr . $a;
                curl_setopt($dz, CURLOPT_URL, $urlAddr);
                curl_multi_add_handle($mh, $dz);
                $handles[] = $dz;

                $data[] = $row;
        }else{
            $tds = $tr->getElementsByTagName('td');

                if($tds->item(8)->textContent == '1 / 1' || $tds->item(8)->textContent =='2 / 1'){
                    continue;
                }            

                $row['nazov'] = $tds->item(2)->textContent;
                $row['typ'] = $tds->item(1)->textContent; 
                $row['veduci'] = $tds->item(3)->textContent;
                $row['garpra'] = $tds->item(4)->textContent;
                $row['program'] = $tds->item(5)->textContent;
                $row['zameranie'] = '--';

                $href = $tds->item(7);
                $a = $href->getElementsByTagName('a')->item(0)->getAttribute('href');
                $dataAhref[count($dataAhref)] = $a;
                
                $data[] = $row;

                $urlBaseAddr = 'https://is.stuba.sk';
                $dz = curl_init();
                curl_setopt($dz, CURLOPT_RETURNTRANSFER, true);
                $urlAddr = $urlBaseAddr . $a;
                curl_setopt($dz, CURLOPT_URL, $urlAddr);
                curl_multi_add_handle($mh, $dz);
                $handles[] = $dz;
        }
    }
    
}

$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running > 0);

foreach ($handles as $i => $handle) {
    $curlAbstract = curl_multi_getcontent($handle);
    $document = new DOMDocument();
    @$document->loadHTML($curlAbstract);

    $trsAbs = $document->getElementsByTagName('tr');
    $trAbs = $trsAbs->item(10);
    $tdsAbs = $trAbs->getElementsByTagName('td');
    $data[$i]['abstract'] = $tdsAbs->item(1)->textContent;

    curl_multi_remove_handle($mh, $handle);
}

curl_multi_close($mh);

echo json_encode($data);

?>