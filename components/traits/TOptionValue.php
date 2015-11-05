<?php

namespace app\components\traits;

/**
 * Trait implementing options value storage for class using it
 */
trait TOptionValue {
    /**
     * Returns option value object
     * @param $name
     * @return array|CActiveRecord|mixed|null
     * @throws Exception
     */
    function getOption($name, $option = null) {
        if (!$option) {
            $option = Option::model()->findByAttributes(['name' => $name]);
        }

        if (!$option) throw new Exception('Invalid option name');

        $value = OptionValue::model()->findByAttributes(['target_id' => $this->id, 'target_type' => $this->getType(), 'option_id' => $option->id]);

        if (!$value) {
            if ($option->default) {
                $value = new OptionValue();
                $value->setAttributes(['target_id' => $this->id, 'target_type' => $this->getType(), 'option_id' => $option->id]);
                $value->value = $option->default;

                return $value;
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Returns option value itself
     * @param $name
     * @return null
     */
    function getOptionValue($name, $option = null) {
        $value = $this->getOption($name, $option);

        return isset($value->value) ? $value->value : $value;
    }

    /**
     * Get values for a group of options
     *
     * @param $groupIds
     */
    function getOptionsGroupsValues($groupIds) {
        $result = [];
        if (!is_array($groupIds)) $groupIds = [$groupIds];

        foreach ($groupIds as $groupId) {
            $result[$groupId] = [];

            $optionsList = Option::model()->findAllByAttributes(['group_id' => $groupId]);

            foreach ($optionsList as $option) {
                $result[$groupId][$option->name] = $this->getOptionValue($option->name, $option);
            }
        }

        return $result;
    }

    /**
     * Crates option value object
     * @param $name
     * @param $value
     * @throws Exception
     */
    function setOption($name, $value) {
        $option = Option::model()->findByAttributes(['name' => $name]);

        $currentValue = $this->getOption($name);

        if ($currentValue && $value != $currentValue->value) {
            if ($value !== false) {
                $currentValue->value = $value;
                $currentValue->save();
            } else {
                $currentValue->delete();
            }
        } elseif($value !== false && !$currentValue) {
            $ov = new OptionValue();
            $ov->setAttributes(['target_id' => $this->id, 'target_type' => $this->getType(), 'option_id' => $option->id]);
            $ov->value = $value;
            $ov->save();
        }

        if (!$option) throw new Exception('Invalid option name');
    }

    function setOptionMulti($options) {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    /**
     * Leaves options storage clean after object deletion
     */
    function dropMyOptions() {
        OptionValue::model()->deleteAllByAttributes(['target_id' => $this->id, 'target_type' => $this->getType()]);
    }
}

?>