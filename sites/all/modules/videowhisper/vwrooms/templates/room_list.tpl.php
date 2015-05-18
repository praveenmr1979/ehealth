<table>
<?php global $base_url;
foreach($data as $row):?>
<?php
if($row['thumb'])
$src=$base_url.'/'.$row['thumb'];
else
$src = url('vwrooms/image/' . $row['type'] . '/' . $row['room'], array('absolute' => true));

?>
<tr><td><img src="<?php echo $src ?>"  alt="<?php echo check_plain($row['thumb'])?>"></img></td><td><?php echo $row['link']?></td></tr>

<?php endforeach;?>

</table>


<?php echo   l(t($text), "node/add/$type", array('attributes'=>array('target'=>'_blank','title'=>t(check_plain($text)))));
?>