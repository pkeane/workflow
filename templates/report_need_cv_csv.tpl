name,EID,department,college,tenure status
{foreach item=fac from=$set}"{$fac->lastname}, {$fac->firstname}",{$fac->eid},"{$fac->dept}","{$fac->college}",{$fac->tenure}	
{/foreach}
