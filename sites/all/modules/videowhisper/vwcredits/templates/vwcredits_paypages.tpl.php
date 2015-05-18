<table cellspacing="5" cellpadding="5">
<tr><th>Package</th><th>Price</th><th>Credits</th><th>Pay</th></tr>
<?php foreach($data as $f):?>

<tr><td><?php echo l("$f->name","vwcredits/packages/pay/$f->pid")?></td><td><?php echo $f->price?></td><td><?php echo $f->credit?></td><td><?php echo l("Pay","vwcredits/packages/pay/$f->pid")?></td></tr>

<?php endforeach;?>

</table>