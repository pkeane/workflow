{extends file="layout.tpl"}

{block name="content"}
<h1>Reports</h1>


<h2>List of Files with a Problem ({$files|@count})</h2>

<ul>
	{foreach item=file from=$files}
	<li>
	<a href="faculty/{$file->eid}/file/{$file->id}">{$file->name}</a> [{$file->problem_note}]
	</li>
	{/foreach}


{/block}
