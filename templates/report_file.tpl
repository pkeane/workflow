{extends file="layout.tpl"}

{block name="content"}
<h1>Reports</h1>


<h2>Edit File</h2>

currently assigned to {$fac->firstname} {$fac->lastname}


<form method="post">
<label for="eid">EID</label>
<input type="text" name="eid" value="{$file->eid}">
<label for="problem_note">Problem Note</label>
<input type="text" name="problem_note" value="{$file->problem_note}">
<p>
<input type="submit" value="update">
</p>
</form>

<h3>Text</h3>
<pre>{$file->rawtext}</pre>

<form method="delete" action="report/file/{$file->id}">
	<input type="submit" value="delete this file">
</form>


{/block}
