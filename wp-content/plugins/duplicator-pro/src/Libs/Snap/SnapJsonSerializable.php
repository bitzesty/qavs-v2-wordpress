<?php

/**
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Libs\Snap;

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

abstract class SnapJsonSerializable
{
    const CLASS_KEY_FOR_JSON_SERIALIZE = '==_CLASS_==_NAME_==';

    protected static function objectToPublicArrayClass($obj = null)
    {
        $reflect = new \ReflectionObject($obj);
        $result  = array(
            self::CLASS_KEY_FOR_JSON_SERIALIZE => $reflect->name
        );

        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            /**
             * get all props of current class but not props private of parent class
             */
            $props = $reflect->getProperties();

            foreach ($props as $prop) {
                $prop->setAccessible(true);
                $propName  = $prop->getName();
                $propValue = $prop->getValue($obj);

                $result[$propName] = self::parseValueToArray($propValue);
            }
        } else {
            $objArray = (array) $obj;
            $re       = '/(?:.*\x00)?(.+)/';
            $subst    = '$1';

            foreach ($objArray as $origPropName => $propValue) {
                $propName          = preg_replace($re, $subst, $origPropName, 1);
                $result[$propName] = self::parseValueToArray($propValue);
            }
        }
        return $result;
    }

    protected static function parseValueToArray($value)
    {
        if (is_object($value)) {
            return self::objectToPublicArrayClass($value);
        } elseif (is_array($value)) {
            $result = array();
            foreach ($value as $key => $arrayVal) {
                $result[$key] = self::parseValueToArray($arrayVal);
            }
            return $result;
        } else {
            return $value;
        }
    }

    protected static function parseArrayToValue($value, $classFromProp = null)
    {
        if (($newClassName = self::getClassFromArray($value, $classFromProp)) !== false) {
            if (class_exists($newClassName)) {
                $newObj = new $newClassName();
            } else {
                $newObj = new \StdClass();
            }

            if (is_subclass_of($newObj, __CLASS__)) {
                $newObj->initFromPublicArray($value, $classFromProp);
            } else {
                $reflect      = new \ReflectionObject($newObj);
                $excludeProps = array(self::CLASS_KEY_FOR_JSON_SERIALIZE);

                $privateProps = $reflect->getProperties(\ReflectionProperty::IS_PROTECTED + \ReflectionProperty::IS_PRIVATE + \ReflectionProperty::IS_STATIC);
                foreach ($privateProps as $pros) {
                    $excludeProps[] = $pros->getName();
                }

                foreach ($value as $arrayProp => $arrayValue) {
                    if (in_array($arrayProp, $excludeProps)) {
                        continue;
                    }
                    $newObj->{$arrayProp} = self::parseArrayToValue($arrayValue, $classFromProp);
                }
            }
            return $newObj;
        } elseif (is_array($value)) {
            $result = array();
            foreach ($value as $key => $arrayVal) {
                $result[$key] = self::parseArrayToValue($arrayVal);
            }
            return $result;
        } else {
            return $value;
        }
    }

    protected function initFromPublicArray($array, $classFromProp = null)
    {
        if (!is_array($array)) {
            return false;
        }

        $reflect        = new \ReflectionObject($this);
        $classFromArray = self::getClassFromArray($array, $classFromProp);

        if ($classFromArray == false || $classFromArray !== $reflect->name) {
            return false;
        }

        $excludeProps = array(self::CLASS_KEY_FOR_JSON_SERIALIZE);
        $privateProps = $reflect->getProperties(\ReflectionProperty::IS_PRIVATE + \ReflectionProperty::IS_STATIC);

        foreach ($privateProps as $pros) {
            $excludeProps[] = $pros->getName();
        }

        foreach ($array as $propName => $propValue) {
            if (in_array($propName, $excludeProps)) {
                continue;
            }
            $this->{$propName} = self::parseArrayToValue($propValue, $classFromProp);
        }
    }

    protected static function getClassFromArray($array, $classFromProp = null)
    {
        if (!is_array($array)) {
            return false;
        } elseif (isset($array[self::CLASS_KEY_FOR_JSON_SERIALIZE])) {
            return $array[self::CLASS_KEY_FOR_JSON_SERIALIZE];
        } elseif (!is_null($classFromProp) && isset($array[$classFromProp])) {
            return $array[$classFromProp];
        } else {
            return false;
        }
    }

    /**
     *
     */
    public function jsonSerialize()
    {
        return SnapJson::jsonEncodePPrint(self::objectToPublicArrayClass($this));
    }

    /**
     *
     * @param string $json
     * @return type
     */
    public static function jsonUnserialize($json, $classFromProp = null)
    {
        $publicArray = json_decode($json, true);
        return self::parseArrayToValue($publicArray, $classFromProp);
    }
}
