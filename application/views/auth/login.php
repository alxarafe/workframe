<!-- Main content -->
<?php echo form_open('auth/login'); ?>
<div class="row">
	<div class="col-sm-6 col-md-4 col-md-offset-4">
		<div class="account-wall">
			<img class="profile-img" src="<?= base_url('img/login.png') ?>"
				alt="">
			<form class="form-signin">
			<?php
			if (isset($message)) echo $message;
			echo form_error('username'); ?>
			<input name="username" type="text" class="form-control" value="<?php echo set_value('username') ?>" placeholder="<?php echo lang('form_username') ?>" required autofocus>
			<?php echo form_error('password'); ?>
			<input name="password" type="password" class="form-control" value="<?php echo set_value('password') ?>" placeholder="<?php echo lang('form_password') ?>" required>
			<label class="checkbox pull-left">
				<input name="remember-me" type="checkbox" value="remember-me"><?php echo lang('form_remember') ?></label>
			<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo lang('form_submit') ?></button>
			<?php /*
			<a href="<?php echo site_url('/auth/reset') ?>" class="pull-right need-help"><?php echo lang('auth_lost_password') ?></a><span class="clearfix"></span>
			*/ ?>
			</form>
		</div>
		<?php /*
		<a href="<?php echo site_url('/auth/newuser') ?>" class="text-center new-account"><?php echo lang('auth_new_user') ?></a>
		*/ ?>
	</div>
</div>
<?php echo form_close(); ?>
