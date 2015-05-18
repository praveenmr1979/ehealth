<div>
<?php echo $pay->name?>
<br/>
<?php echo $pay->dscr?><br/>


<?php if($pay->business):?>
<a href="<?php echo url("vwcredits/pay/paypal/$pay->pid")?>">Pay Using paypal</a>&nbsp;&nbsp;
<?php endif;?>

<?php if($pay->plimus_cid):?>
<a href="<?php echo url("vwcredits/pay/plimus/$pay->pid")?>">Pay Using plimus</a>
<?php endif;?>

</div>