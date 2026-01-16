
<form action="<?= url_to('otp/verify') ?>" method="post">
<input type="hidden" name="phone" id="phone" value="<?= $phone ?>">
<input type="number" name="code" id="code"  required>
<button type="submit">submit</button></br>
<span ><?= !empty($error) ? $error:'' ?></span>
</form>

