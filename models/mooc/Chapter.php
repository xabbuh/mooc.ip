<?php
namespace Mooc;

/**
 * @author  <mlunzena@uos.de>
 */
class Chapter extends AbstractBlock
{
    public function __construct($id = null) {

        $this->belongs_to['courseware'] = array(
            'class_name' => 'Mooc\\Courseware',
            'foreign_key' => 'parent_id');

        $this->has_many['sections'] = array(
            'class_name' => 'Mooc\\Section',
            'assoc_foreign_key' => 'parent_id',
            'assoc_func' => 'findByParent_id');

        parent::__construct($id);
    }
}
