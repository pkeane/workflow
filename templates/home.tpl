{extends file="layout.tpl"}

{block name="content"}

<form method="get" action="search">
	<label for="q">Search Faculty:</label>
	<input type="text" name="q" value="{$q}"> 
	<input type="submit" value="go">
	<input type="checkbox" value="1" {if $restrict_to_have}checked{/if} name="restrict_to_have"> restrict to "we have CV" |
	<input type="checkbox" value="1" {if $sort}checked{/if} name="sort">  unit sort |
	<input type="radio" name="tenure" {if 'NT' == $tenure}checked{/if} value="NT"> NT
	<input type="radio" name="tenure" {if 'TN' == $tenure}checked{/if} value="TN"> TN
	<input type="radio" name="tenure" {if 'TT' == $tenure}checked{/if} value="TT"> TT
	<input type="radio" name="tenure" {if 'RT' == $tenure}checked{/if} value="RT">	RT
	<input type="radio" name="tenure" {if 'none' == $tenure}checked{/if} value="none"> none	
	<input type="radio" name="tenure" {if 'any' == $tenure}checked{/if} value="0"> any	
</form>


<h2>{$result_message}</h2>

<table id="faculty">
	<tr>
		<th>name</th>
		<th>EID</th>
		<th>department</th>
		<th>college</th>
		<th>tenure status</th>
		<th>CV</th>
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
		<td>
			{if $fac->have_cv}&#10003;{/if}	
		</td>
	</tr>

	{/foreach}
</table>




{/block}
