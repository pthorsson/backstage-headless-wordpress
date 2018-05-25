<div class="wrap">
	<h1>Endpoints</h1>

	<p>Filter what endpoints that should be exposed in the WordPress REST API.</p>

	<p>
		<hr>
	</p>

	<div class="bs-hook_init-spinner bs_admin-loading" style="display: block;">
		<span>Loading settings ...</span>
		<div class="spinner"></div>
	</div>

	<div class="bs-hook_content" style="display: none;">
		<label>
			<input name="enable_endpoints" id="enable_endpoints" class="bs-hook_enable-endpoints-check" type="checkbox" value="false">
			<b>Enable endpoint filter</b>
		</label>

		<h2 class="title">Endpoints</h2>

		<p class="bs-hook_endpoints-disabled">
			Endpoint filter is disabled
		</p>

		<table style="border-spacing: 0;" class="bs-endpoints__table bs-hook_endpoints-table">
			<thead>
				<tr class="bs-cors__table-row">
					<td>
						<input name="endpoint_check_all" id="endpoint_check_all" class="bs-hook_all-endpoints-check" type="checkbox" value="false">
					</td>
					<td>
						<label for="endpoint_check_all">All endpoints</label>
					</td>
				</tr>
			</thead>
			<tbody class="bs-hook_endpoints-list" style="display: none;">
				<tr class="bs-cors__table-row bs-hook_table-row">
					<td>
						<input name="endpoint_check_" id="endpoint_check_" class="bs-hook_endpoint-check" type="checkbox" value="false">
					</td>
					<td>
						<label for="endpoint_check_">
							<div>
								<code class="bs-hook_endpoint-label"></code>
							</div>
							<div class="bs-hook_endpoint-methods"></div>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="bs-hook_status-message"></p>

		<p class="submit">
			<button class="button button-primary bs-hook_save-changes">Save changes</button>
			<button class="button bs-hook_reset-changes">Reset</button>
		</p>
	</div>

</div>