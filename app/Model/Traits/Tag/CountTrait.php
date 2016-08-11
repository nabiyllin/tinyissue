<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Model\Traits\Tag;

/**
 * CountTrait is trait class containing the methods for counting database records for the Tag model.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @property static $this
 */
trait CountTrait
{
    /**
     * Count number of tags.
     *
     * @return int
     */
    public function count()
    {
        return $this->where('group', '=', false)->count();
    }
}
