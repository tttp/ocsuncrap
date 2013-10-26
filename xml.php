<?
$keys= array ("country","postal.code","address","city","date.of.birth","lastname","nationality","firstname");
$prefix = strlen("oct.property.");


$file=$argv[1];

if (!$file) {
  die ("USAGE: \$php xml.php file.xml for a single file\n or \$php xml.php folder\n");
}


if (is_dir($file)) {
  $total = 0;
  $csv= $argv[1].".csv";
  $fp = fopen($csv, 'w');
  $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($file));
  foreach($objects as $name => $object){
    if (".xml" == substr ($name,-4)) {
//    if ("_dec.xml" == substr ($name,-8)) {
      $s = xml2csv($fp,$name);
      $total +=$s;
      echo "\n $s signatures in $name";
    }
  }
} else {
  $csv=substr ($argv[1],0,-3)."csv";
  $fp = fopen($csv, 'w');
  $total = xml2csv($fp,$file);
}
echo "\n $total signatures in total in file $fp\n";
fclose($fp);



function group($g,&$r) {
  global $keys,$prefix;
  foreach ($g->properties->property as $p) {
    if (in_array (substr($p->key,$prefix),$keys)) {
      $r[substr($p->key,$prefix)] = (string)$p->value;
    }
  }
}

function xml2csv ($fp, $file) {
  $total = 0;
  $xml = simplexml_load_file ($file);
  foreach ($xml->signatures->signature as $s) {
    $total++;
    $result["date"]= substr($s->submissionDate,0,-6);
    foreach ($s->signatoryInfo->groups->group as $g) {
      group ($g,$result);
    }
    fputcsv($fp,$result);
  }
  return $total;
}
