<?php namespace yii\behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Class JsonBehavior
 *
 * @package common\components
 * @author Vitaly Dubovik <dev.vitaly@gmail.com>
 *
 * @property ActiveRecord $owner
 */
class JsonBehavior extends Behavior
{
	public $attributeName;
	public $mapClassName;

	/**
	 * @return array
	 */
	public function events()
	{
		return [
			BaseActiveRecord::EVENT_INIT => 'import',
			BaseActiveRecord::EVENT_BEFORE_INSERT => 'export',
			BaseActiveRecord::EVENT_BEFORE_UPDATE => 'export',
			BaseActiveRecord::EVENT_AFTER_UPDATE => 'import',
			BaseActiveRecord::EVENT_AFTER_FIND => 'import',
			BaseActiveRecord::EVENT_AFTER_INSERT => 'import',
		];
	}

	/**
	 * @return mixed
	 */
	public function get()
	{
		return $this->owner->getAttribute($this->attributeName);
	}

	/**
	 * @param $value
	 */
	public function set($value)
	{
		$this->owner->setAttribute($this->attributeName, $value);
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		try {
			return strtolower($this->owner->getTableSchema()->getColumn($this->attributeName)->type);
		} catch (InvalidConfigException $e) {
			return 'string';
		}
	}

	/**
	 * Export value
	 */
	public function export()
	{
		$value = $this->get();

		if ($value instanceof Model) {
			$value = $value->toArray();
		}

		if ($this->getType() !== 'json') {
			$value = json_encode($value);
		}

		$this->set($value);
	}

	/**
	 * Import value
	 */
	public function import()
	{
		$data = $this->get();

		if (!class_exists('\yii\db\JsonExpression')) {
			$data = json_decode($data, true);
		}

		if (is_string($data)) {
			$data = json_decode($data, true);
		}

		if (!empty($this->mapClassName)) {
			$class = $this->mapClassName;
			$data = new $class($data);
		}

		$this->set($data);
	}
}