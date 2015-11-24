/**
 * This array is used to remember mark status of rows in browse mode
 */
var marked_row = new Array;

/**
 * enables highlight and marking of rows in data tables
 *
 */
function PMA_markRowsInit() {
	// for every table row ...
	var rows = document.getElementsByTagName('tr');
	for ( var i = 0; i < rows.length; i++ ) {
		// ... with the class 'odd' or 'even' ...
		if ( 'odd' != rows[i].className.substr(0,3) && 'even' != rows[i].className.substr(0,4) ) {
			continue;
		}
		// ... add event listeners ...
		// ... to highlight the row on mouseover ...
		if ( navigator.appName == 'Microsoft Internet Explorer' ) {
			// but only for IE, other browsers are handled by :hover in css
			rows[i].onmouseover = function() {
				this.className += ' hover';
			}
			rows[i].onmouseout = function() {
				this.className = this.className.replace( ' hover', '' );
			}
		}
		// Do not set click events if not wanted
		if (rows[i].className.search(/noclick/) != -1) {
			continue;
		}
		// ... and to mark the row on click ...
		rows[i].onmousedown = function() {
			var unique_id;
			var checkbox;

			checkbox = this.getElementsByTagName( 'input' )[0];
			if ( checkbox && checkbox.type == 'checkbox' ) {
				unique_id = checkbox.name + checkbox.value;
			} else if ( this.id.length > 0 ) {
				unique_id = this.id;
			} else {
				return;
			}

			if ( typeof(marked_row[unique_id]) == 'undefined' || !marked_row[unique_id] ) {
				marked_row[unique_id] = true;
			} else {
				marked_row[unique_id] = false;
			}

			if ( marked_row[unique_id] ) {
				this.className += ' marked';
			} else {
				this.className = this.className.replace(' marked', '');
			}

			if ( checkbox && checkbox.disabled == false ) {
				checkbox.checked = marked_row[unique_id];
			}
		}

		// ... and disable label ...
		var labeltag = rows[i].getElementsByTagName('label')[0];
		if ( labeltag ) {
			labeltag.onclick = function() {
				return false;
			}
		}
		// .. and checkbox clicks
		var checkbox = rows[i].getElementsByTagName('input')[0];
		if ( checkbox ) {
			checkbox.onclick = function() {
				// opera does not recognize return false;
				this.checked = ! this.checked;
			}
		}
	}
}
window.onload=PMA_markRowsInit;
