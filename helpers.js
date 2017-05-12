function cf7emojione_current_line(textarea) {
	var ta = jQuery(textarea),
	//pos = jQuery(ta).getSelection().start, // fieldselection jQuery plugin
	pos =  jQuery(ta).prop("selectionStart"),
	taval = ta.val(),
	start = taval.lastIndexOf('\n', pos - 1) + 1,
	end = taval.indexOf('\n', pos);

	if (end == -1) {
		end = taval.length;
	}
	var currentline=taval.substr(start, end - start);
	if(currentline.length<2) { return false; }
	return currentline;
}

var emojiStrategy=undefined;

jQuery(document).ready(function($) {

	emojiStrategy=(emojione.emojioneList);

	jQuery('textarea.values').textcomplete([ {
			match: /\B:([\-+\w]*)$/,
			search: function (term, callback) {
				var results = [];
				var results2 = [];
				var results3 = [];
				$.each(emojiStrategy,function(basename,data) {
					//if(data.shortname.indexOf(term) > -1) { results.push(basename); }
					if(basename.indexOf(term) > -1) { results.push(basename); }
					// else {
					//     if((data.shortname_alternates !== null) && (data.shortname_alternates.indexOf(term) > -1)) {
					//         results2.push(basename);
					//     }
					//     else if((data.keywords !== null) && (data.keywords.indexOf(term) > -1)) {
					//         results3.push(basename);
					//     }
					// }
				});

				// if(term.length >= 3) {
				//     results.sort(function(a,b) { return (a.length > b.length); });
				//     results2.sort(function(a,b) { return (a.length > b.length); });
				//     results3.sort();
				// }
				// var newResults = results.concat(results2).concat(results3);

				callback(results);
			},
			template: function (basename) {
				return emojione.toImage(basename)+emojione.toShort(basename)+'';
				//return '<img class="emojione" src="https://cdn.jsdelivr.net/emojione/assets/3.0/png/32/'+emojiStrategy[basename].fname+'.png"> '+emojione.toShort(basename)+'';
			},
			replace: function (basename) {
				return basename+"\n";
			},
			index: 1,
			maxCount: 10
		}
		],{
			zIndex: "999999",
			footer: '<a href="http://www.emoji.codes" target="_blank">Browse All<span class="arrow">»</span></a>'
		});

});

