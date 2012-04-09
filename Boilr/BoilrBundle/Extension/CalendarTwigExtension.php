<?php

namespace Boilr\BoilrBundle\Extension;

class CalendarTwigExtension extends \Twig_Extension
{
    const MAX_EVENTS_IN_CELL = 3;

    public function getFunctions()
    {
        return array(
            'calendar' => new \Twig_Function_Method($this, 'calendar', array('is_safe' => array('html'))),
        );
    }

    public function calendar($year, $month, array $events, $weekNum = false)
    {
        if (! is_numeric($year) || ! is_numeric($month)) {
            return null;
        }

        $monthName = date("F", strtotime("01-$month-1970"));
        $date1     = date('Y-m-d', strtotime("first day of $monthName $year"));
        $date2     = date('Y-m-d', strtotime("last day of $monthName $year"));
        $current   = new \DateTime($date1);
        $interval  = \DateInterval::createFromDateString('1 day');
        $endDate   = new \DateTime($date2);

        $html = '<ol class="calendar">';

        // previous month
        $dayOfWeek = $current->format('N');
        if ($dayOfWeek > 1) {
            $html .= '<li id="lastmonth"><ol>';
            $numDays = $dayOfWeek;
            $subInt  = \DateInterval::createFromDateString($numDays .' day');
            $aDay = clone $current;
            $aDay->sub($subInt);

            for ($j = 1; $j < $numDays; $j++) {
                $aDay->add($interval);
                $html .= '<li>'. $aDay->format('d') .'</li>';
            }

            $html .= '</ol></li>';
        }

        // current month
        $html .= '<li id="thismonth"><ol>';
        while ($current <= $endDate) {
            $day  = $current->format('d');
            $html .= '<li>' . $current->format('d'). $this->getEventTitle($events, $day) .'</li>';
            $current->add($interval);
        }
        $html .= '</ol></li>';

        // next month
        $endDate->add($interval);
        $dayOfWeek = $endDate->format('N');
        if ($dayOfWeek < 7) {
            $html .= '<li id="nextmonth"><ol>';
            $aDay  = clone $endDate;

            for ($j = $dayOfWeek; $j <= 7; $j++) {
                $html .= '<li>'. $aDay->format('d') .'</li>';
                $aDay->add($interval);
            }
            $html .= '</ol></li>';
        }

        $html .= '</ol>';

        return $html;
    }

    /*
    public function calendar($year, $month, array $events, $weekNum = false)
    {
        if (! is_numeric($year) || ! is_numeric($month)) {
            return null;
        }

        $monthName = date("F", strtotime("01-$month-1970"));
        $date1     = date('Y-m-d', strtotime("first day of $monthName $year"));
        $date2     = date('Y-m-d', strtotime("last day of $monthName $year"));
        $current   = new \DateTime($date1);
        $interval  = \DateInterval::createFromDateString('1 day');
        $endDate   = new \DateTime($date2);

        // Building table header and title
        $title     = strftime('%B %Y', strtotime("first day of $monthName $year"));
        $tableId   = "calendar_".$year."_".$month;
        $html      = '<div class="calendar_container">'.
                     '<span class="calendar_header">'.$title.'</span>';
        $html     .= '<table id="'.$tableId.'" class="calendar">'.
                     '<thead><tr><th>Lun</th><th>Mar</th><th>Mer</th><th>Gio</th>'.
                     '<th>Ven</th><th>Sab</th><th>Dom</th></tr></thead><tbody>';

        while ($current <= $endDate) {
            $html .= '<tr>';
            if ($weekNum) {

            }

            $dayOfWeek = $current->format('N');
            $day       = $current->format('d');

            for ($i=1; $i<$dayOfWeek; $i++) {
                $class = ($i>5?'weekend':'working');
                $html .= '<td class="'.$class.'">&nbsp;</td>';
            }

            for ($i=$dayOfWeek; $i<8; $i++) {
                $dayOfWeek = $current->format('N');
                $day       = $current->format('d');
                $class     = ($dayOfWeek>5?'weekend':'working');
                $html     .= '<td class="'.$class.'">';

                if ($current > $endDate) {
                    $html .= '&nbsp;';
                } else {
                    // get all events belonging to current cell/day
                    $content = $this->getEventTitle($events, $day);

                    // check if there are too many events
                    if (count($content) > self::MAX_EVENTS_IN_CELL) {

                    } else {
                        $html .= '<span class="day">'.$day.'</span>';
                    }

                    $html .= $content;
                }
                $html .= '</td>';

                $current = $current->add($interval);
            }

            $html .= '</tr>';
        }

        // Close table tag
        $html .= '</tbody></table></div>';

        return $html;
    }
    */

    private function getEventTitle($records, $day)
    {
        $value = null;

        if (array_key_exists($day, $records)) {
            $titles = $records[$day];
            $count  = max(array(count($titles), self::MAX_EVENTS_IN_CELL));

            $value = "<ul>";
            for ($i=0; $i < $count; $i++) {
                $value .= $titles[$i];
            }
            $value .= "</ul>";
        }

        return $value;
    }

    public function getName()
    {
        return 'calendar_twig_extension';
    }

}