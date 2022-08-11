<?php

namespace app\components;

use yii\base\Behavior;

/**
 * Поведение делает возможным запрашивать объект связанный с моделью
 * 
 * @author Uniser NB Arterim <uniserpl@gmail.com>
 * @since 2022.08.11
 */
class DynObjBehavior extends Behavior
{
    const NS_OBJECT_CLASS = 'app\\models\\obj\\';
    const PREFIX_OBJ_RELATION = 'obj';

    private static function getRelatedObjectClass($name) {
        if (substr($name,0,3) === self::PREFIX_OBJ_RELATION) {
            $class = self::NS_OBJECT_CLASS . substr($name,3);
            if (class_exists($class)) {
                return $class;
            }
        }
        return null;
    }
    
    public function canGetProperty($name, $checkVars = true) {
        return self::getRelatedObjectClass($name)
            ? true
            : parent::canGetProperty($name, $checkVars);
    }
    
    public function __get($name) {
        $class = self::getRelatedObjectClass($name);
        if (empty($class)) {
            return parent::__get($name);
        }
        if (lcfirst(substr($name,3)) !== $this->owner->getAttribute('object')) {
            // Если запрошенный и реальный объект не совпадают
            return null;
        }
        return $this->owner->hasOne($class, ['id' => 'object_id']);
    }
}
