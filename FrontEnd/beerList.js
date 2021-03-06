function insertBeerTable(parentElement){
	//var endpoint = 'http://localhost/BeerList/BackEnd/Beer.php';
	var endpoint = 'http://boredknight.com/beerlist/webcalls/beer.php';
	var table = '<table id="beerListingTable"><thead style="text-align: left;"><tr>'+
					'<th> Name </th>' +
					'<th> Style </th>' +
					'<th> ABV </th>' +
					'<th> Brewery </th>' +
					'<th> Location </th>' +
				'</tr></thead><tbody id="beerListBody"></tbody></table>';
	$(parentElement).append(table);

	$.ajax({
        url: endpoint,
        success: function(data) {
            for (var i = 0; i < data.results.length; i++){
            	var newRow = '<tr>'+
            					'<td>' + data.results[i].name + '</td>'  +
            					'<td>' + data.results[i].style + '</td>'  +
            					'<td>' + data.results[i].abv + '% </td>'  +
            					'<td>' + data.results[i].brewery + '</td>'  +
            					'<td>' + data.results[i].location + '</td>'  +
            				'</tr>';
            	$('#beerListBody').append(newRow);
            }
        }
    });
}
//document.addEventListener("DOMContentLoaded", function(event) { 
	if (typeof jQuery == 'undefined') {
		if (typeof $ == 'function') {
			// warning, global var
			thisPageUsingOtherJSLibrary = true;
		}
		function getScript(url, success) {
			var script = document.createElement('script');
			    script.src = url;
			var head = document.getElementsByTagName('head')[0],
			done = false;
			
			// Attach handlers for all browsers
			script.onload = script.onreadystatechange = function() {
				if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == '	complete')) {
				done = true;
					// callback function provided as param
					success();
					script.onload = script.onreadystatechange = null;
					head.removeChild(script);
				};
			};
			head.appendChild(script);
		};
		
		getScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js', function() {
			if (typeof jQuery=='undefined') {
				// Super failsafe - still somehow failed...
			} else {
				// jQuery loaded! Make sure to use .noConflict just in case
				//fancyCode();
				//if (thisPageUsingOtherJSLibrary) {
					// Run your jQuery Code
					//insertBeerTable($('body'));
					insertBeerTable($('.entry-content'));
				//} else {
					// Use .noConflict(), then run your jQuery Code
				//}
			}
		});
	} else { // jQuery was already loaded
		// Run your jQuery Code
		//insertBeerTable($('body'));
		insertBeerTable($('.entry-content'));
	};
//});

/*
function getScript(url) {
	var script = document.createElement('script');
	    script.src = url;
	var head = document.getElementsByTagName('head')[0],
	done = false;
	head.appendChild(script);
};
getScript('http://boredknight.com/beerlist/beerList.js');

*/