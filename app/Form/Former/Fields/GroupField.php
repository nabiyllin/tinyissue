<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Form\Former\Fields;

use Former\Traits\Field;

/**
 * GroupField is a Former field class to generate a group of fields as one field.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class GroupField extends Field
{
    /**
     * A list of class properties to be added to attributes.
     *
     * @var array
     */
    protected $injectedProperties = [];

    /**
     * The field's default element.
     *
     * @var string
     */
    protected $element = 'div';

    /**
     * List of managed fields.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Set managed fields.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function fields(array $fields)
    {
        array_walk($fields, function (&$field, $name) {
            $field = \Form::element($this->name . '[' . $name . ']', $field);
            $field->setAttribute('id', $this->name . '-' . $name);
        });
        $this->fields = $fields;

        return $this;
    }

    /**
     * Render the field.
     *
     * @return string
     */
    public function render()
    {
        $this->addClass('group-fields');
        $this->setId();
        $this->removeAttribute('value');

        return $this->open() . $this->getContent() . $this->close();
    }

    /**
     * Render the field content. Rendering the managed fields.
     *
     * @return string
     */
    public function getContent()
    {
        $currentLabelWidths = \Former::getOption('TwitterBootstrap3.labelWidths');
        \Former::setOption('TwitterBootstrap3.labelWidths', [
            'large' => 0,
        ]);

        $output = '';
        foreach ($this->fields as $field) {
            $output .= $field->__toString();
        }

        \Former::setOption('TwitterBootstrap3.labelWidths', $currentLabelWidths);

        return $output;
    }

    /**
     * Returns values stored in managed fields.
     *
     * @return array
     */
    public function getValue()
    {
        if (!is_array($this->fields)) {
            return [];
        }

        return array_map(function (Field $field) {
            return $field->getValue();
        }, $this->fields);
    }

    /**
     * Set the matching ID on a field if possible
     * Override to prefix the id with group-.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getUniqueId($name)
    {
        return 'group-' . parent::getUniqueId($name);
    }
}
