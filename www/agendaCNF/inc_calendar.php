<?php
/* draws a calendar */
/* http://davidwalsh.name/php-calendar	*/
function draw_calendar($month, $year, $agenda)
{
	$annee_courante = date("Y", time()) ;
	$mois_courant = date("m", time()) ;
	$jour_courant = date("d", time()) ;
	$highlight = false ;
	if ( ($month == $mois_courant) and ($year == $annee_courante) ) {
		$highlight = true ;
	}

	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	$headings = array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
	$calendar .= '<tr class="calendar-row"><td class="calendar-day-head">'
		. implode('</td><td class="calendar-day-head">',$headings)
		. '</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		if ( $highlight and ($list_day == $jour_courant) ) {
			$calendar.= '<td class="calendar-day highlighted">';
		}
		else {
			$calendar.= '<td class="calendar-day">';
		}
		/* add in the day number */
		$calendar.= '<div class="day-number">'.$list_day.'</div>';

		/************   AGENDA ************/
		if ( isset($list_day) AND isset($agenda[$list_day]) AND is_array($agenda[$list_day]) ) {
			foreach($agenda[$list_day] as $ev) {
				$calendar.= "<div class='ev'>" ;
				if ( intval($_SESSION["id"]) == 0 ) {
					$calendar.= "<a href='indispo.php?action=maj&id_indispo="
						. $ev["id_indispo"]."'>" ;
				}
				$calendar.= "<div class='co'>" ;
				$calendar.= nl2br($ev["commentaire"]) ;
				$calendar.= "</div>" ;
				$calendar.= "<div class='li'>" ;
				$calendar.= $ev["liste"] ;
				$calendar.= "</div>" ;
				if ( intval($_SESSION["id"]) == 0 ) {
					$calendar.= "</a>" ;
				}
				$calendar.= "</div>" ;
			}
		}
		else {
			$calendar.= "<p>&nbsp;</p>" ;
		}

		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';

	return $calendar;
}

/* sample usages */
//echo '<h2>10 2011</h2>';
//echo draw_calendar(10,2011);

?>
