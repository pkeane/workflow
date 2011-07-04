name,EID,department,college,tenure status,note
{foreach item=fac from=$facs}"{$fac->lastname}, {$fac->firstname}",{$fac->eid},"{$fac->dept}","{$fac->college}",{$fac->tenure},"{$fac->problem_note}"	
{/foreach}
