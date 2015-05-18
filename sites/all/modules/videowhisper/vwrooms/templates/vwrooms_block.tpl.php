<table>

<?php
/*
 * $r[o]: owner name
 * $r[a] :Access Public/Private
 * $r[c] :Cost
 * $r[t] :Room Type
 *
 */
$ch=variable_get('vwrooms_showblockh',0);//show extra info


foreach($data as $n){

	$txt="";
	if($n['t'])
	$txt.=" $n[t] ";
	if($n['c'])
	$txt.= t('Cost').' :'.$n['c'].' ';
	if($n['o'])
	$txt.= t('Owner').' :'.$n['o'].' ';
	if($n['a'])
	$txt.= $n['a'];

?>
<tr><td>
<?php if($ch):?>
 <a href="<?php echo $n['ln']?>" class="tooltip" title="<?php echo  htmlspecialchars($txt) ?>"><?php echo $n['r']?>(<?php echo (int)$n['oc']?>)<span>&nbsp;&nbsp;</span></a>
<?php else:?>
<a href="<?php echo $n['ln']?>"><?php echo $n['r']?>(<?php echo (int)$n['oc']?>)</a>
<?php endif;?>
</td></tr>
	<?php
}

?></table>
<?php
echo l(t("More.."),"vwrooms/list");