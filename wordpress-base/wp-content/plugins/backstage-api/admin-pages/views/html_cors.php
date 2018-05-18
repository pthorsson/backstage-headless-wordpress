<div class="wrap">
	<h1>CORS settings</h1>

	<form name="form" action="/wp-admin/admin.php?page=backstage-api-cors" method="post">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="babc106f55">
		<input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=backstage-api-cors">
		<input type="hidden" name="cors_settings" id="cors_settings" type="text" value="<?php echo $json_data ?>" class="regular-text code">
		<p>Here you can choose if you want to enable CORS for your REST API. Add the origins you want to allow in the list below.</p>

		<p>
			<hr>
		</p>

		<label>
			<input name="enable_cors" id="enable_cors" type="checkbox" value="custom" checked="checked">
			<b>Enable CORS</b>
		</label>

		<h2 class="title">Allowed origins</h2>

		<table style="border-spacing: 0;" class="bs-origins-table">
			<tbody>
				<tr>
					<td style="border-bottom: 1px solid lightgrey; padding: 5px 0;">
						<code>http://localhost</code>
					</td>
					<td style="border-bottom: 1px solid lightgrey; padding: 5px 0;">
						<a href="javascript:void(0);" class="button button-small">Remove</a>
					</td>
				</tr>

				<tr>
					<td style="border-bottom: 1px solid lightgrey; padding: 5px 0;">
						<code>http://localhost</code>
					</td>
					<td style="border-bottom: 1px solid lightgrey; padding: 5px 0;">
						<a href="javascript:void(0);" class="button button-small">Remove</a>
					</td>
				</tr>

				<tr>
					<td style="border-bottom: 1px solid lightgrey; padding: 5px 0;">
						<code>http://localhost</code>
					</td>
					<td style="border-bottom: 1px solid lightgrey; padding: 5px 0;">
						<a href="javascript:void(0);" class="button button-small">Remove</a>
					</td>
				</tr>

				<tr id="bs-cors__add">
					<td style="padding: 5px 0;">
						<input id="permalink_sstructure" type="text" class="regular-text code" placeholder="Example: http://my.domain.com">
					</td>
					<td style="padding: 5px 0;">
						<a href="javascript:void(0);" class="button button-small button-primary">Add</a>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
		</p>
	</form>

</div>