Credit for User <?php echo $user->name?><br/>
<table cellpadding='5' cellspacing='5'>
<tr><th>Current Balance</th><td><?php echo $bal?></td></tr>
<tr><th>Available Balance</th><td><?php echo $abal?></td></tr>
</table>
<br/>
Current balance less unprocessed withdraw request=Available Balance.
<br/>
<?php if($min):?>
Minimum withdrawable amount <?php echo $min ?><hr/><br/>
<?php endif;?>

<?php if($pending):?>
You have a pending withdrawal request of <?php echo $pending ?> to your paypal a/c <?php echo $paypal?><br/>
Click <a href="<?php echo url('vwcredits/payments/creditlist',array('query'=>array('cancel'=>1)))?>">Here</a> to cancel this request<br/>


<?php else:?>

<?php echo variable_get('vwcredits_withdrawp', '');?>

<?php echo $form;?>
<?php endif;?>
