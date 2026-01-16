    <h2 class="mt-5 display-5">OTP PLUGIN</h2>
    <hr>
    <?php if (env('AUTH_MODE') == 'phone') { ?>
        <div class="mb-3">
            <label for="" class="form-label">Enter your phone number .</label>
            <div class="row justify-content-md-center">
                <form action="<?= url_to('otp/send') ?>" method="POST">
                    <div class="tfa_confirm_container">
                        <input type="text" name="phone" required placeholder="+1234567890"></br>
                        <span><?= !empty($error) ? $error: ''; ?></span>
                        <button type="submit">send</button>
                    </div>
                </form>
            </div>
        </div>
    <?php  }; ?>
    <?php if (env('AUTH_MODE') == 'email') { ?>
        <div class="mb-3">
            <label for="" class="form-label">Enter your email id .</label>
            <div class="row justify-content-md-center">
                <form action="<?= url_to('otp/send') ?>" method="POST">
                    <div class="tfa_confirm_container">
                        <input type="email" name="email" required placeholder="sujeet@gmail.com"></br>
                        <span><?= !empty($error) ? $error: ''; ?></span>
                        <button type="submit">send</button>
                    </div>
                </form>
            </div>
        </div>
    <?php  }; ?>