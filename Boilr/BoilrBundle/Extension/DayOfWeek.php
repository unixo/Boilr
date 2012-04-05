<?php

namespace Boilr\BoilrBundle\Extension;

class DayOfWeek extends \SplEnum
{
    const __default = self::Monday;

    const Monday    = 1;
    const Tuesday   = 2;
    const Wednesday = 3;
    const Thursday  = 4;
    const Friday    = 5;
    const Saturday  = 6;
    const Sunday    = 7;
}