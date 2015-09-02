<?php

namespace Baseapp\Library;

/**
 * Class Time
 * ####################################################################
 * This class is a kind of helper class full of time specific functions.
 * There are time converters, formatters etc. These functions are also
 * available for use in views just by calling the function()
 *
 * @TODO Add documentation
 *
 * @package Baseapp\Library
 */

class Time
{


    /**
     * Get the actual server time.
     * This may be different from the time you get if you simply use time();
     *
     * @return int
     */
    public static function serverTime()
    {
        // Mon Sep 23 14:52:04 EDT 2013
        $sdate = str_replace('  ', ' ', shell_exec('date')); // remove double space when day is only one character
        $sdate = explode(' ', $sdate);
        $stime = explode(':', $sdate[3]);
        $server_time = mktime($stime[0], $stime[1], $stime[2], date('n', time()), trim($sdate[2]), trim($sdate[5]));
        return $server_time;
    }


    /**
     * sql_now() will simply output the current time formatted as a
     * MySQL datetime stamp. Can optionally be passed a unix time stamp
     *
     * @param bool|FALSE $option
     * @return bool|int|string
     */
    public static function sql_now($option = FALSE)
    {
        if ($option == 'stamp') { // for unix time stamp
            $time = time();
        } else {
            $time = date('Y-m-d H:i:s', time()); // sql time
        }
        return $time;
    }


    // for inserting any custom time into db compensated for time difference
    // accepts unix time stamp
    // useful for time calculations
    // option 'stamp' outputs unix time stamp
    public static function sql_time($time_stamp = FALSE, $option = FALSE)
    {
        if ($option == 'stamp') { // for unix time stamp
            $time = $time_stamp;
        } else {
            $time = date('Y-m-d H:i:s', $time_stamp); // sql time
        }
        return $time;
    }


    /**
     * from_sql() format MySQL time to a more human friendly format.
     *
     * @param bool|FALSE $time
     * @param bool|FALSE $option
     * @return bool|int|string
     */
    public static function from_sql($time = FALSE, $option = FALSE)
    {
        // check if a time exists - return nothing if not
        if ($time) { // <- WTF is this for?
            // check if we are working with unix time stamp or sql time
            if (is_numeric($time)) {
                $stamp = $time;
            } else {
                $spdate = explode(' ', $time);
                $sdate = explode('-', trim($spdate[0]));
                $stime = explode(':', trim($spdate[1]));
                $stamp = mktime($stime[0], $stime[1], $stime[2], $sdate[1], $sdate[2], $sdate[0]);
            }
            if ($option == 'short') { // e.g. 21 March 2013 4:45pm
                $display = date('d M Y g:ia', $stamp);
                if (ISSET($userinfo) && $userinfo['timezone'] == TRUE) {
                    $timezone = new DateTimeZone($userinfo['timezone']);
                    $date = new DateTime('@' . $stamp, $timezone);
                    $date->setTimezone($timezone);
                    $display = $date->format('d M Y g:ia');
                    //$display = $myDateTime;
                }
            }else if($option == 'vshort'){
                $display = date('g:ia d/m/y ', $stamp);
                if (ISSET($userinfo) && $userinfo['timezone'] == TRUE) {
                    $timezone = new DateTimeZone($userinfo['timezone']);
                    $date = new DateTime('@' . $stamp, $timezone);
                    $date->setTimezone($timezone);
                    $display = $date->format('g:iad/m/y ');
                    //$display = $myDateTime;
                }
            } else {
                $display = date('D d F Y g:ia', $stamp);
                if (ISSET($userinfo['timezone']) && $userinfo['timezone'] == TRUE) {
                    // e.g. Friday 21 March 2013 4:45pm
                    $timezone = new DateTimeZone($userinfo['timezone']);
                    $date = new DateTime('@' . $stamp, $timezone);
                    $date->setTimezone($timezone);
                    $display = $date->format('D d M Y g:ia');
                }
            }
            if ($option == 'fixed') { // unchanged (as posted by user) mainly for event dates
                $display = date('l jS F Y g:ia', $stamp);
            }
            if ($option == 'date') {
                $display = date('d M Y', $stamp); // date only
            }
            if ($option == 'time') {
                $display = date('h:ia', $stamp); // time only
            }
            if ($option == 'numeric') {
                $display = date('d/m/Y g:ia', $stamp); // numeric date and time e.g. 21/5/2016 5:50pm
            }
            if ($option == 'numeric date') {
                $display = date('d/m/Y', $stamp); // numeric date only e.g. 21/5/2016 5:50pm
            } else if ($option == 'stamp') {
                return $stamp;
            }
        } else {
            $display = '';
        }
        return $display;
    }



    /**
     * Time list - generate a list of times from start and end time
     * @param $start = time eg 20:00
     * @param $end
     * @param $step
     */
    public static function time_list($start, $end, $step = FALSE)
    {
        if ($step == FALSE) {
            $step = 30;
        }
        $increment = ($step * 60);
        //convert to full times
        $start_time = date('Y-m-d', time()) . ' ' . $start . ':00';
        $end_time = date('Y-m-d', time()) . ' ' . $end . ':00';
        //echo $start_time . ' ' . $end_time;exit;
        // convert to unix stamps
        $start_stamp = Time::from_sql($start_time, 'stamp');
        $end_stamp = Time::from_sql($end_time, 'stamp');
        //echo $start_time;exit;
        //echo date('Y-m-d H:i', $start_stamp + $increment);exit;

        $list = array();
        $inc = $start_stamp;
        while ($end_stamp >= $inc) {
            $list[] = date('H:i', $inc);
            $inc += $increment;
        }
        return $list;
    }



    /**
     * Generate timezone list
     * Ronseal - does exactly what it says on the tin!
     *
     * @return array
     */
    public static function tz_list() {
        $zones_array = array();
        $timestamp = time();
        foreach(timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);
            $zones_array[$key]['zone'] = $zone;
            $zones_array[$key]['offset'] = (int) ((int) date('O', $timestamp))/100;
            $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
        }
        return $zones_array;
    }

}
