<?php
namespace Bit0\Web\h2o
{
    /**
     * Filters short summary.
     *
     * Filters description.
     *
     * @version 1.0
     * @author Jain
     */
    class Filters
    {
        public function __construct()
        {
            \h2o::addFilter('\\'.__NAMESPACE__.'\TranslateFilter');
            \h2o::addFilter('\\'.__NAMESPACE__.'\MathFilter');
             
        }
    }
    
    class TranslateFilter extends \FilterCollection
    {
        public static function TransF()
        {
            $args = func_get_args();
            switch (count($args))
            {
                case 1:
                    return $args[0];
                default:
                    $forrmat = array_shift($args);
                    return vsprintf($forrmat, $args);
            }
            
        }
        
        public static function TransN($number, $plural, $single, $second = null, $third = null)
        {
            switch ($number)
            {
                case 1:
                    return sprintf($single, $number);
                case 2:
                    if ($second != null)
                        return sprintf($second, $number);
                case 3:
                    if ($third != null)
                        return sprintf($third, $number);
                default:
                    return sprintf($plural, $number);
            }
        }
        
    }
    
    class MathFilter extends \FilterCollection
    {
        public static function Math($value, $type, $num)
        {
            switch ($type)
            {
                case '/':
                case 'divide':
                    return self::DivideBy($value, $num);
                case '-':
                    return self::Substract($value, $num);
                case '*':
                    return self::MultiplyBy($value, $num);
                default:
                    return self::Add($value, $num);
            }
        }
        
        public static function Add($value, $num)
        {
            return $value + $num;
        }
        
        public static function Substract($value, $num)
        {
            return $value - $num;
        }
        
        public static function MultiplyBy($value, $num)
        {
            return $value * $num;
        }
        
        public static function DivideBy($value, $num)
        {
            return $value / $num;
        }
    }
}