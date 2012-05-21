<?php defined("SYSPATH") or die("No direct script access."); ?>
<div class="g-block">
	<h1><?= t("CAS settings") ?></h1>
	<div class="g-block-content">

		<?= $form ?>

		<div class="ui-helper-clearfix"></div>

		<? if ($cas_enabled) { ?>
			<form method="post" action="<?= url::site("admin/cas_settings/disable") ?>" style="display:inline">
				<?= access::csrf_form_field() ?>
				<button type="submit" class="submit ui-state-default ui-corner-all">Disable</button>
			</form>
		<? } else { ?>
			<form method="post" action="<?= url::site("admin/cas_settings/enable") ?>" style="display:inline">
				<?= access::csrf_form_field() ?>
				<button type="submit" class="submit ui-state-default ui-corner-all">Enable</button>
			</form>
		<? } ?>
	</div>
</div>
