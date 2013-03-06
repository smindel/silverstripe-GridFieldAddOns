<% require css(GridFieldAddOns/css/GridFieldUserColumns.css) %>

<h1>Verfügbare Spalten</h1>
<p><small>Sortierung per Drag&amp;Drop</small></p>

<ul>
<% loop $Columns %>
	<li>
		<input type="checkbox" name="columns[{$Name}]" id="columns[{$Name}]" value="{$Name}:{$Title}"<% if $Selected %> checked="checked"<% end_if %>>
		<label for="columns[{$Name}]">$Title</label>
	</li>
<% end_loop %>
</ul>