{extends file="layout.tpl"}

{block name="content"}
<h1>Reports</h1>


<h2>List of Faculty without no status on at least 1 file ({$set|@count})</h2>

<table id="faculty">
	<tr>
		<th>name</th>
		<th>EID</th>
		<th>department</th>
		<th>college</th>
		<th>tenure status</th>
		<!--
		<th>CV</th>
		-->
		<th></th>
	</tr>
	{foreach item=fac from=$set}

	<tr>
		<td>
			<a href="faculty/{$fac->eid}">{$fac->lastname}, {$fac->firstname}</a>
		</td>
		<td>
			<a href="faculty/{$fac->eid}">{$fac->eid}</a>
		</td>
		<td>
			<a href="faculty/{$fac->eid}">{$fac->dept}</a>
		</td>
		<td>
			<a href="faculty/{$fac->eid}">{$fac->college|truncate:30}</a>
		</td>
		<td>
			<a href="faculty/{$fac->eid}">{$fac->tenure}</a>
		</td>
		<!--
		<td>
			{if $fac->have_cv}&#10003;{/if}	
		</td>
		-->
		<td>
			{if in_array($fac->eid,$request->user->watchlist|@array_keys)}
			<a href="user/{$request->user->eid}/watchlist/{$fac->eid}" class="delete">unwatch</a>
			{else}
			<a href="user/{$request->user->eid}/watchlist/{$fac->eid}" class="put">watch</a>
			{/if}
		</td>
	</tr>

	{/foreach}
</table>




{/block}
