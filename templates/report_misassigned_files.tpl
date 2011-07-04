{extends file="layout.tpl"}

{block name="content"}
<h1>Reports</h1>


<h2>List of Files with a Problem ({$files|@count})</h2>

<ul>
	{foreach item=file from=$files}
	<li>
	<a href="faculty/{$file->eid}/file/{$file->id}">{$file->name}</a> | EID: {$file->eid} | [{$file->problem_note}] |
	<a href="report/file/{$file->id}" class="edit">Edit CV</a>
	</li>
	{/foreach}


{/block}
