<?php

namespace Boilr\BoilrBundle\Extension;

class CalendarTwigExtension extends \Twig_Extension
{
    public function __construct()
    {
    }

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
                    $html .= '<span class="day">'.$day.'</span><br/>';

                    if ( ($content = $this->getEventTitle($events, $day)) ) {
                        $html .= $content;
                    }
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

    private function getEventTitle($records, $day)
    {
        $value = null;

        if (array_key_exists($day, $records)) {
            $titles = $records[$day];

            for ($i=0; $i<count($titles); $i++) {
                $value .= $titles[$i];
            }
        }

        return $value;
    }

    public function getName()
    {
        return 'calendar_twig_extension';
    }

}