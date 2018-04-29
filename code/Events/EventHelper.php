<?php
namespace TitleDK\Calendar\Events;

/**
 * Event Helper
 * Helper class for event related calculations and formatting
 *
 * @package calendar
 */
class EventHelper
{


    /**
     * Formatted Dates
     * Returns either the event's date or both start and end date if the event spans more than
     * one date
     *
     * Format:
     * Jun 7th - Jun 10th
     *
     * @param SS_Datetime $startObj
     * @param SS_Datetime $endObj
     * @return string
     */
    public static function formatted_dates($startObj, $endObj)
    {
        //Checking if end date is set
        $endDateIsset = isset($endObj);

        $startTime = strtotime($startObj->value);
        $endTime = strtotime($endObj->value);

        $startMonth = date('M', $startTime);

        // include ordinal, e.g. 1st, 4th
        $startDayOfMonth = $startObj->DayOfMonth(true) ;

        $str = $startMonth . ' ' . $startDayOfMonth ;

        if (date('Y-m-d', $startTime) == date('Y-m-d', $endTime)) {
            //one date - str. has already been written
        } else {
            //two dates

            if ($endDateIsset) {
                $endMonth = date('M', $endTime);

                // include ordinal, e.g. 1st, 4th
                $endDayOfMonth = $endObj->DayOfMonth(true);

                if ($startMonth == $endMonth) {
                    $str .= ' - ' . $endDayOfMonth;
                } else {
                    $str .= ' - ' . $endMonth . ' ' . $endDayOfMonth;
                }
            }
        }
        return $str;
    }

    public static function formatted_alldates($startObj, $endObj)
    {
        $startDate = date("Y-m-d", strtotime($startObj->value));
        $endDate = date("Y-m-d", strtotime($endObj->value));

        if ($startDate == $endDate) {
            return false;
        }

        $startTime = strtotime($startObj->value);
        $endTime = strtotime($endObj->value);

        // @todo This should be a separate helper method
        // @todo Make standard date format configurable
        if (date('g:ia', $startTime) == '12:00am') {
            $startDate = date('jS M F, Y', $startTime);
        } else {
            $startDate = date('jS M, Y (g:ia)', $startTime);
        }

        // @todo see note above
        if (date('g:ia', $endTime) == '12:00am') {
            $endDate = date('jS M, Y', $endTime);
        } else {
            $endDate = date('jS M, Y (g:ia)', $endTime);
        }

        return $startDate." &ndash; ".$endDate;
    }

    /**
     * Formatted time frame
     * Returns either a string or null
     * Time frame is only applicable if both start and end time is on the same day
     * @param string $startStr
     * @param string $endStr
     * @return string|null
     */
    public static function formatted_timeframe($startStr, $endStr)
    {
        $str = null;

        if ($startStr == $endStr) {
            return null;
        }

        $startTime = strtotime($startStr->value);
        $endTime = strtotime($endStr->value);

        if ($startTime == $endTime) {
            return null;
        }

        if ($endStr) {
            //time frame is only applicable if both start and end time is on the same day
            if (date('Y-m-d', $startTime) == date('Y-m-d', $endTime)) {
                $str = date('g:ia', $startTime) . ' - ' . date('g:ia', $endTime);
            }
        } else {
            $str = date('g:ia', $startTime);
        }

        return $str;
    }
}
