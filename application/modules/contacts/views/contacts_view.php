<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			    <strong><?=lang('label.contact_fillform')?></strong>
			</div>
			<div class="panel-body" id="contact_us">
				<center><div id="error_div"></div></center>
			    <form class="form-horizontal" role="form" method="post" action="<?php echo base_url();?>contacts/submit">
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label" style="color: black;"><?=lang('label.contact_name')?></label>
						<div class="col-sm-10">
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="<?=lang('label.contact_firstlastname')?>" required>
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-sm-2 control-label" style="color: black;"><?=lang('label.contact_email')?></label>
						<div class="col-sm-10">
							<input type="email" class="form-control" id="email" name="email" placeholder="example@domain.com" required>
						</div>
					</div>
					<div class="form-group">
						<label for="phone" class="col-sm-2 control-label" style="color: black;"><?=lang('label.contact_phone')?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="phone" name="phone" placeholder="<?=lang('label.contact_phone_number_ph')?>" required>
						</div>
					</div>
					<div class="form-group">
						<label for="subject" class="col-sm-2 control-label" style="color: black;"><?=lang('label.contact_subject')?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="subject" name="subject" placeholder="<?=lang('label.contact_subject')?>" required>
						</div>
					</div>
					<div class="form-group">
						<label for="message" class="col-sm-2 control-label" style="color: black;"><?=lang('label.contact_message')?></label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="4" id="message" name="message" required></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-4 col-sm-offset-2">
							<input id="btnSubmit" name="btnSubmit" type="submit" value="<?=lang('label.contact_submit')?>" class="btn btn-primary" style="color:white; background-color:#1BA39C;">
						</div>
						<div class="col-sm-6" id="loading"></div>
					</div>
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-2">
						</div>
					</div>
					<div class="g-recaptcha" data-sitekey="6LcQyDkUAAAAAB6Qx3q3aT1768kpNQ7EGkok-pUj"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('contacts_view_footer.php');?>
