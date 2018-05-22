<div class="wrap">
	<h1>CORS settings</h1>

	<p>Here you can choose if you want to enable CORS for your REST API. Add the origins you want to allow in the list below.</p>

	<p>
		<hr>
	</p>

	<p class="backstage_spinner">
		<span>Loading</span>
		<div class="spinner"></div>
	</p>

	<label>
		<input name="enable_cors" id="enable_cors" class="bs-hook_enable-cors-check" type="checkbox" value="custom">
		<b>Enable CORS</b>
	</label>

	<h2 class="title">Allowed origins</h2>

	<table style="border-spacing: 0;" class="bs-cors__table bs-hook_origins-table">
		<tfoot>
			<tr>
				<td>
					<input type="text" class="bs-cors__input regular-text code bs-hook_origin-input bs-hook_interactive-element" placeholder="Example: http://my.domain.com">
				</td>
				<td>
					<button class="button button-small button-primary bs-hook_add-origin bs-hook_interactive-element">Add</button>
				</td>
			</tr>
		</tfoot>
		<tbody class="bs-hook_added-origins" style="display: none;">
			<tr class="bs-cors__table-row bs-hook_table-row">
				<td>
					<code class="bs-hook_origin-label"></code>
				</td>
				<td>
					<button class="button button-small bs-hook_remove-origin bs-hook_interactive-element">Remove</button>
				</td>
			</tr>
		</tbody>
	</table>

	<p class="bs-hook_status-message"></p>

	<p class="submit">
		<button class="button button-primary bs-hook_save-origins">Save changes</button>
	</p>
</div>