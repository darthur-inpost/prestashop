{if isset($type)}
<form class="form-horizontal well" role="form">
	{if $type == 'badges_feature' || $type == 'badges_achievement'}
		<div class="form-group">
			<label>{l s="Type:" mod='inpostshipping'}</label>
			<select id="group_select_{$type}" onchange="filterBadge('{$type}');">
					<option value="badge_all">{l s="All" mod='inpostshipping'}</option>
			</select>
		</div>
	{/if}
		<div class="form-group">
			<label>{l s="State:" mod='inpostshipping'}</label>
			<select id="status_select_{$type}" onchange="filterBadge('{$type}');">
				<option value="badge_all">{l s="All" mod='inpostshipping'}</option>
				<option value="validated">{l s="Validated" mod='inpostshipping'}</option>
				<option value="not_validated">{l s="Not Validated" mod='inpostshipping'}</option>
			</select>
		</div>
		<div class="form-group">
			<label>{l s="Level:" mod='inpostshipping'}</label>
				<select id="level_select_{$type}" onchange="filterBadge('{$type}');">
						<option value="badge_all">{l s="All" mod='inpostshipping'}</option>
				</select>
		</div>
</form>
<div class="clear"></div>
{/if}
