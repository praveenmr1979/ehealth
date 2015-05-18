<?php

/*
 * $data is array such that
 * foreach($data as $r)
 *
 * where
$r['l']=room link
$r['r']=room type
$r['t']=thum image
$r['d']=description (100 letters)
$r['u']=No of users online
$r['o']=owner
$r['oc']=Owner online
$r['a']=Access
$r['c']=Cost
$r['g]=Tags
 *
 *
 *similarly $header contain the header elements according to theme_table format

 * and customize this theme as you need.
 * pager theme variables shouldn't be changed.
 *
 */



// Add sticky headers, if applicable.
if (count($header)) {
	drupal_add_js('misc/tableheader.js');
	// Add 'sticky-enabled' class to the table to identify it for JS.
	// This is needed to target tables constructed by this function.
	$attributes['class'] = empty($attributes['class']) ? 'sticky-enabled' : ($attributes['class'] .' sticky-enabled');
}

$output = '<table'. drupal_attributes($attributes) .">\n";

if (isset($caption)) {
	$output .= '<caption>'. $caption ."</caption>\n";
}
$hds=$header;
$header=array();
$header1=array();
if($hds['e']){
$header[]=$hds['e'];
$header1[]=$hds['d'];
}

if($hds['r']){
	$header[]=$hds['r'];
	$header1[]=$hds['g'];
}


if($hds['t']){
	$header[]=$hds['t'];
	$header1[]=$hds['x'];
}
if($hds['u']){
	$header[]=$hds['u'];
	$header1[]=$hds['x'];
}
if($hds['o']){
	$header[]=$hds['o'];
	$header1[]=$hds['n'];
}
if($hds['a']){
	$header[]=$hds['a'];
	$header1[]=$hds['c'];
}








// Format the table header:
if (count($header)) {
	$ts = tablesort_init($header);
	// HTML requires that the thead tag has tr tags in it followed by tbody
	// tags. Using ternary operator to check and see if we have any rows.
	$output .= (count($rows) ? ' <thead><tr>' : ' <tr>');
	foreach ($header as $cell) {
		$cell = tablesort_header($cell, $header, $ts);
		$output .= _theme_table_cell($cell, TRUE);
	}
	$output.="</tr><tr>";
	$ts = tablesort_init($header1);

	foreach ($header1 as $cell) {
		$cell = tablesort_header($cell, $header, $ts);
		$output .= _theme_table_cell($cell, TRUE);
	}

	// Using ternary operator to close the tags based on whether or not there are rows
	$output .= (count($rows) ? " </tr></thead>\n" : "</tr>\n");
}
else {
	$ts = array();
}
$rows=$data;

// Format the table rows:
if (count($rows)) {
	$output .= "<tbody>\n";
	$flip = array('even' => 'odd', 'odd' => 'even');
	$class = 'even';
	foreach ($rows as $number => $row) {
		$attributes = array();
$cells=$cell1=array();
		// Check if we're dealing with a simple or complex row

///			$cells = array($row['e'],$row['r'],$row['t'],$row['u'],$row['o'],$row['a']);
if($hds['e']){
	$cells[]=$row['l'];
	$cell1[]=$row['d'];

}
if($hds['r']){
	$cells[]=$row['r'];
	$cell1[]=$row['g'];
}


if($hds['t']){
	$cells[]=$row['t'];
}
if($hds['u']){
	$cells[]=$row['u'];
}
if($hds['o']){
	$cells[]=$row['o'];
	$cell1[]=$row['oc'];
}
if($hds['a']){
	$cells[]=$row['a'];
	$cell1[]=$row['c'];
}




		if (count($cells)) {
			// Add odd/even class
			$class = $flip[$class];
			if (isset($attributes['class'])) {
				$attributes['class'] .= ' '. $class;
			}
			else {
				$attributes['class'] = $class;
			}

			// Build row
			$output .= ' <tr'. drupal_attributes($attributes) .'>';
			$i = 0;
			foreach ($cells as $cell) {
			///	$cell = tablesort_cell($cell, $header, $ts, $i++);
				$output .= _theme_table_cell($cell);
			}
			$output .= " </tr>\n";


			$output .= ' <tr'. drupal_attributes($attributes) .'>';
			$i = 0;
			foreach ($cell1 as $cell) {

				$output .= _theme_table_cell($cell);
			}
			$output .= " </tr>\n";

		}


	}
	$output .= "</tbody>\n";
}

$output .= "</table>\n";
echo $output;





?>