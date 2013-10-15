<?
$file=$argv[1];
$csv=substr ($argv[1],0,-3)."csv";
$fp = fopen($csv, 'w');

$xml = simplexml_load_file ($file);
$keys= array ("country","postal.code","address","city","date.of.birth","lastname","nationality","firstname");
$prefix = strlen("oct.property.");
foreach ($xml->signatures->signature as $s) {
  $result["date"]= substr($s->submissionDate,0,-6);
  foreach ($s->signatoryInfo->groups->group as $g) {
    group ($g,$result);
  }
  fputcsv($fp,$result);
}
fclose($fp);

function group($g,&$r) {
  global $keys,$prefix;
  foreach ($g->properties->property as $p) {
    if (in_array (substr($p->key,$prefix),$keys)) {
      $r[substr($p->key,$prefix)] = (string)$p->value;
    }
  }
}
