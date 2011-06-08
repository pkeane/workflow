{extends file="layout.tpl"}

{block name="content"}
<h1>Reports</h1>


<h2>List of Faculty with a Problem ({$facs|@count})</h2>

<ul>
	{foreach item=f from=$facs}
	<li>
	<a href="faculty/{$f->eid}">{$f->firstname} {$f->lastname} ({$f->eid})</a> [{$f->problem_note}]
	</li>
	{/foreach}


{/block}
