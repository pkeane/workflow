var Dase = {};

$(document).ready(function() {
	Dase.initHighlighting();
	Dase.initDelete('topMenu');
	Dase.initDelete('facinfo');
	Dase.initDelete('poss_dups');
	Dase.initDelete('faculty');
	Dase.initPut('facinfo');
	Dase.initPut('faculty');
	Dase.initPut('lines');
	Dase.initToggle('lines');
	Dase.initToggle('target');
	Dase.initToggle('email');
	Dase.initToggle('cv');
	Dase.initToggle('raw');
	Dase.initSortable('target');
	Dase.initUserPrivs();
	Dase.initFormDelete();
	Dase.initProbCodeInsert();
	Dase.initCodeInsert();
	Dase.initPossDups();
	Dase.initShowDups();
});

Dase.initToggle = function(id) {
	$('#'+id).find('a[class="toggle"]').click(function() {
		var id = $(this).attr('id');
		var tar = id.replace('toggle','target');
		$('#'+tar).toggle();
		return false;
	});	
};

Dase.initHighlighting = function() {
	var hl = location.href.split('#')[1];
	$('#'+hl).addClass('highlight');
};


Dase.initShowDups = function() {
	$('#lines').find('a[class="show_line_form"]').click(function() {
		var id = $(this).attr('id');
		var tar = id.replace('toggle','target');
		$('#'+tar).toggle();

		var dupcount = $(this).attr('data-dupcount');
		if (parseInt(dupcount)) {
			var target = $(this).parents('li').find('ul');
			var _o = {
				'url': $(this).attr('href'),
				'type':'GET',
				'success': function(data) {
					var dups = '';
					for (var i=0; i<data.length; i++) {
						var line= data[i];
						dups += '<li>'+line.text+' <a href="line/'+line.id+'/diff" class="diff_link">[diff]</a></li>';
					}
					target.html(dups);
					Dase.initGetDupDiff(target);
				},
				'error': function() {
					alert('sorry, there was an error');
				}
			};
			$.ajax(_o);
		}
		return false;
	});	
};

Dase.initGetDupDiff = function(ul) {
	ul.find('a[class="diff_link"]').click(function() {
		var _o = {
			'url': $(this).attr('href'),
			'type':'GET',
			'success': function(data) {
				data = '<p class="diff">'+data+' <a href="#" class="op">[hide]</a></p>';
				ul.after(data);
				ul.parents('li').find('a[class="op"]').click(function() {
					$(this).parents('p').remove();
					return false;
				});
			},
			'error': function() {
				//pass
			}
		};
		$.ajax(_o);
		return false;
	});
};

Dase.initPossDups = function() {
	$('#poss_dups').find('a[class="dup"]').click(function() {
		//if (confirm('are you sure it is a duplicate?')) {
			var _o = {
				'url': $(this).attr('href'),
				'type':'POST',
				'success': function() {
					location.reload();
				},
				'error': function() {
					alert('sorry, there was an error');
				}
			};
			$.ajax(_o);
		//}
		return false;
	});
	$('#poss_dups').find('a[class="no_dup"]').click(function() {
		//if (confirm('are you sure it is NOT a duplicate?')) {
			var _o = {
				'url': $(this).attr('href'),
				'type':'POST',
				'success': function() {
					location.reload();
				},
				'error': function() {
					alert('sorry, there was an error');
				}
			};
			$.ajax(_o);
		//}
		return false;
	});
	$('#poss_dups').find('a[class="not_cite"]').click(function() {
		//if (confirm('are you sure it is NOT a citation?')) {
			var _o = {
				'url': $(this).attr('href'),
				'type':'POST',
				'success': function() {
					location.reload();
				},
				'error': function() {
					alert('sorry, there was an error');
				}
			};
			$.ajax(_o);
		//}
		return false;
	});
};

Dase.initProbCodeInsert = function() {
	$('select[name="problem_code"]').change( function() {
		var code = $(this).find("option:selected").text();
		$(this).parents('form').find('input[name="problem_note"]').attr('value',code);
	});
};

Dase.initCodeInsert = function() {
	$('select[name="code"]').change( function() {
		var code = $(this).find("option:selected").text();
		$('input[name="note"]').attr('value',code);
	});
};

Dase.initFormDelete = function() {
	$("form[method='delete']").submit(function() {
		if (confirm('are you sure?')) {
			var del_o = {
				'url': $(this).attr('action'),
				'type':'DELETE',
				'success': function() {
					location.reload();
				},
				'error': function() {
					alert('sorry, cannot delete');
				}
			};
			$.ajax(del_o);
		}
		return false;
	});
};

Dase.initDelete = function(id) {
	$('#'+id).find("a[class='delete']").click(function() {
		if (confirm('are you sure?')) {
			var del_o = {
				'url': $(this).attr('href'),
				'type':'DELETE',
				'success': function(resp) {
					if (resp.location) {
						location.href = resp.location;
					} else {
						location.reload();
					}
				},
				'error': function() {
					alert('sorry, cannot delete');
				}
			};
			$.ajax(del_o);
		}
		return false;
	});
};

Dase.initPut = function(id) {
	$('#'+id).find("a[class='put']").click(function() {
		var del_o = {
			'url': $(this).attr('href'),
			'type':'PUT',
			'success': function(resp) {
				if (resp.location) {
					location.href = resp.location;
				} else {
					location.reload();
				}
			},
			'error': function() {
				alert('sorry, cannot delete');
			}
		};
		$.ajax(del_o);
		return false;
	});
};

Dase.initSortable = function(id) {
	$('#'+id).sortable({ 
		cursor: 'crosshair',
		opacity: 0.6,
		revert: true, 
		start: function(event,ui) {
			ui.item.addClass('highlight');
		},	
		stop: function(event,ui) {
			$('#proceed-button').addClass('hide');
			$('#unsaved-changes').removeClass('hide');
			$('#'+id).find("li").each(function(index){
				$(this).find('span.key').text(index+1);
			});	
			ui.item.removeClass('highlight');
		}	
	});
};
 
Dase.initUserPrivs = function() {
	$('#user_privs').find('a').click( function() {
		var method = $(this).attr('class');
		var url = $(this).attr('href');
			var _o = {
				'url': url,
				'type':method,
				'success': function(resp) {
					alert(resp);
					location.reload();
				},
				'error': function() {
					alert('sorry, there was a problem');
				}
			};
			$.ajax(_o);
		return false;
	});
};

