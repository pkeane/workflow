{extends file="layout.tpl"}

{block name="content"}
<h1>Reports</h1>


<h2>List of Faculty with NO Preferred Versions ({$facs|@count})</h2>

<table id="faculty">
	<tr>
		<th>name</th>
		<th>EID</th>
		<th>department</th>
		<th>college</th>
		<th>tenure status</th>
	</tr>
	{foreach item=fac from=$facs}

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
	</tr>

	{/foreach}
</table>

{/block}
